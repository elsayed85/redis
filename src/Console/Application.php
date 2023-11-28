<?php

namespace Elsayed85\LmsRedis\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    const VERSION = '1.0.0';

    protected array $commands = [
        //
    ];

    public function __construct()
    {
        parent::__construct('LmsRedis', self::VERSION);
        $this->setCommands();
    }

    public function setCommands(): void
    {
        $commands = glob(__DIR__.'/Commands/*/*.php');

        foreach ($commands as $command) {
            $command = str_replace('.php', '', $command);
            $command = str_replace(__DIR__.'/Commands/', '', $command);
            $command = str_replace('/', '\\', $command);
            $this->commands[] = 'Elsayed85\\LmsRedis\\Console\\Commands\\'.$command;
        }

        $this->addCommands(array_map(fn ($command) => new $command, $this->commands));
    }
}
