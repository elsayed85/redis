<?php

namespace Elsayed85\LmsRedis\Facades;

use Illuminate\Support\Facades\Facade;

class Redis extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lms-redis';
    }
}
