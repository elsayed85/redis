<?php

namespace Elsayed85\LmsRedis\Services\RatingService;

use Elsayed85\LmsRedis\LmsRedis;
use Elsayed85\LmsRedis\Traits\HasEvents;

class RatingRedisService extends LmsRedis
{
    use HasEvents;

    public function getServiceName(): string
    {
        return 'rating';
    }
}
