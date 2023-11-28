<?php

namespace Elsayed85\LmsRedis\Services\ProductService;

use Elsayed85\LmsRedis\LmsRedis;
use Elsayed85\LmsRedis\Traits\HasEvents;

class ProductRedisService extends LmsRedis
{
    use HasEvents;

    public function getServiceName(): string
    {
        return 'product';
    }
}
