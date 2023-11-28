<?php

namespace Elsayed85\LmsRedis\Commands;

use Illuminate\Console\Command;

class LmsRedisInstallCommand extends Command
{
    public $signature = 'lms:redis:install';

    public $description = 'Install Redis';

    public function handle(): int
    {
        $this->info('Installing Redis...');

        if (! $this->installRedis()) {
            $this->error('Redis installation failed');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function getOs(): string
    {
        $os = PHP_OS;
        if (strtoupper(substr($os, 0, 3)) === 'WIN') {
            return 'windows';
        }

        return 'linux';
    }

    private function installRedis(): bool
    {
        $os = $this->getOs();
        if ($os === 'windows') {
            return $this->installRedisOnWindows();
        } else {
            return $this->installRedisOnLinux();
        }
    }

    private function installRedisOnWindows(): bool
    {
        if (shell_exec('redis-server --version')) {
            $this->info('Redis is already installed');

            return true;
        }

        $this->info('please install redis manually from https://redis.io/docs/getting-started/installation/install-redis-on-windows/');

        return false;
    }

    private function installRedisOnLinux(): bool
    {
        if (shell_exec('redis-server --version')) {
            $this->info('Redis is already installed');

            return true;
        }

        try {
            $this->info('Installing Redis...');

            $this->info('Updating apt-get...');
            shell_exec('sudo apt-get update');

            $this->info('Installing Redis...');
            shell_exec('sudo apt-get install redis-server');

            $this->info('Redis installed successfully');
        } catch (\Exception $e) {
            $this->error('Redis installation failed');

            return false;
        }

        return false;
    }
}
