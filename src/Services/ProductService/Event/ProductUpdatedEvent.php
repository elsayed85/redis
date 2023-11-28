<?php

namespace Elsayed85\LmsRedis\Services\ProductService\Event;

use Elsayed85\LmsRedis\Services\Event;
use Elsayed85\LmsRedis\Services\ProductService\DTO\ProductData;
use Elsayed85\LmsRedis\Services\ProductService\Enum\ProductEvent;

class ProductUpdatedEvent extends Event
{
    public ProductEvent $type = ProductEvent::UPDATED;

    public function __construct(public readonly ProductData $data)
    {
    }
}
