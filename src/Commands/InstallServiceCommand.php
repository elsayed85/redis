<?php

namespace Elsayed85\LmsRedis\Commands;

use Elsayed85\LmsRedis\LmsRedis;
use Elsayed85\LmsRedis\Utils\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;

class InstallServiceCommand extends Command
{
    public $signature = 'lms:install';

    public $description = 'List all services';

    public function handle(): void
    {
        $services = collect(Service::getAllServices());
        $names = $services->map(fn ($service) => class_basename($service));
        $class = $this->selectService($names);
        $class = $services->first(fn ($service) => class_basename($service) === $class);

        $this->publishVendorConfig();

        $config = $this->getConfig($class);
        file_put_contents(config_path('lms-redis.php'), $config);

        $this->publishVendorConsumeCommand();

        $service = $this->getService($class);

        $events = $class::events();
        $functions = $this->getFunctions($events);

        $eventsImports = $this->getEventsImports($events);
        $pramtersImports = $this->getParametersImports($events);

        $service = $this->replaceImports($service, $eventsImports, $pramtersImports);
        $service = $this->replaceFunctions($service, $functions);

        $this->createServiceFile($class, $service);
    }

    private function selectService($names): string
    {
        return select(
            label: 'Which service do you want to install?',
            options: $names->toArray(),
        );
    }

    private function publishVendorConfig(): void
    {
        $this->call('vendor:publish', [
            '--provider' => 'Elsayed85\LmsRedis\LmsRedisServiceProvider',
            '--tag' => 'lms-redis-config',
        ]);
    }

    private function publishVendorConsumeCommand(): void
    {
        $this->call('vendor:publish', [
            '--provider' => 'Elsayed85\LmsRedis\LmsRedisServiceProvider',
            '--tag' => 'lms-redis-consume-command',
        ]);
    }

    private function getConfig($class)
    {
        $config = file_get_contents(config_path('lms-redis.php'));

        return str_replace(
            LmsRedis::class,
            $class,
            $config
        );
    }

    private function getService($class)
    {
        $serviceFile = __DIR__.'/../Stubs/RedisService.stub';
        $service = file_get_contents($serviceFile);
        $service = str_replace(
            'BaseService::class',
            $class.' as BaseService',
            $service
        );

        return str_replace(
            'ServiceName',
            class_basename($class),
            $service
        );
    }

    private function getFunctions($events)
    {
        $functionStub = file_get_contents(__DIR__.'/../Stubs/PublishEventFunction.stub');
        $functions = '';
        foreach ($events as $event => $parameters) {
            $function = $functionStub;
            $function = str_replace('{EventName}', class_basename($event), $function);
            $function = str_replace('parmeter_class_and_variable', implode(', ', array_map(function ($param) {
                return class_basename($param).' $'.Str::camel(class_basename($param));
            }, $parameters)), $function);
            $function = str_replace('pramters', implode(', ', array_map(function ($param) {
                return '$'.Str::camel(class_basename($param));
            }, $parameters)), $function);
            $functions .= $function;
        }

        return $functions;
    }

    private function getEventsImports($events)
    {
        return array_map(function ($event) {
            return 'use '.$event.';';
        }, array_keys($events));
    }

    private function getParametersImports($events)
    {
        $pramters = array_values($events);
        $pramters = array_unique(array_merge(...$pramters));

        return array_map(function ($param) {
            return 'use '.$param.';';
        }, $pramters);
    }

    private function replaceImports($service, $eventsImports, $pramtersImports)
    {
        $service = str_replace(
            '// events_import_here',
            implode("\n", $eventsImports),
            $service
        );

        return str_replace(
            '// pramters_import_here',
            implode("\n", $pramtersImports),
            $service
        );
    }

    private function replaceFunctions($service, $functions)
    {
        $functions = "\t".str_replace("\n", "\n\t", $functions);
        $functions = str_replace("}\n\t", "}\n\n\t", $functions);

        return str_replace(
            '// functions_here',
            $functions,
            $service
        );
    }

    private function createServiceFile($class, $service)
    {
        $servicesDirectory = app_path('Services');
        if (! \File::exists($servicesDirectory)) {
            \File::makeDirectory($servicesDirectory, 0755, true);
        }
        $newServiceFile = $servicesDirectory.'/'.class_basename($class).'.php';
        \File::put($newServiceFile, $service);
    }
}
