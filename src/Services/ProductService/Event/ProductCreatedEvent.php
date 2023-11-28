<?php

namespace Elsayed85\LmsRedis\Services\ProductService\Event;

use Elsayed85\LmsRedis\Services\Event;
use Elsayed85\LmsRedis\Services\ProductService\DTO\ProductData;
use Elsayed85\LmsRedis\Services\ProductService\Enum\ProductEvent;

class ProductCreatedEvent extends Event
{
    public ProductEvent $type = ProductEvent::CREATED;

    public function __construct(public readonly ProductData $data)
    {
    }
}
