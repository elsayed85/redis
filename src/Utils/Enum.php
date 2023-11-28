<?php

namespace Elsayed85\LmsRedis\Utils;

class Enum
{
    private static function getEnumFiles($enumsNamespace)
    {
        $dir = __DIR__.'/../Services';
        $files = glob($dir.'/*/Enum/*.php');
        $namespace = 'Elsayed85\\LmsRedis\Services\\';
        $classes = array_map(function ($file) use ($dir, $namespace) {
            $folder = str_replace([$dir.'/', '/'], ['', '\\'], dirname($file));
            $file = $namespace.$folder.'\\'.pathinfo($file, PATHINFO_FILENAME);

            return class_exists($file) ? $file : null;
        }, $files);

        return array_filter($classes);
    }

    private static function all()
    {
        $services = collect(Service::getAllServices());

        return $services
            ->map(function ($service) {
                $service = substr($service, 0, strrpos($service, '\\')).'\\Enum';

                return self::getEnumFiles($service);
            })
            ->flatten()
            ->mapWithKeys(function ($file) {
                return [$file => $file::cases()];
            })
            ->toArray();
    }

    public static function From($type): ?object
    {
        $enums = self::all();
        foreach ($enums as $enum) {
            $case = collect($enum)->firstWhere('value', $type);
            if ($case !== null) {
                return $case;
            }
        }

        return null;
    }
}
