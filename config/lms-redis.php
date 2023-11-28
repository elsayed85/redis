<?php

return [
    'service' => \Elsayed85\LmsRedis\LmsRedis::class,

    'redis' => [
        'client' => 'phpredis',

        'options' => [
            'cluster' => 'redis',
            'prefix' => 'database_',
        ],

        'default' => [
            'url' => null,
            'host' => '127.0.0.1',
            'username' => null,
            'password' => null,
            'port' => '6379',
            'database' => '0',
        ],

        'cache' => [
            'url' => null,
            'host' => '127.0.0.1',
            'username' => null,
            'password' => null,
            'port' => '6379',
            'database' => '1',
        ],
    ],
];
