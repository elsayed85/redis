<?php

namespace Elsayed85\LmsRedis\Services\ProductService\Enum;

enum ProductEvent: string
{
    case CREATED = 'product:created';
    case UPDATED = 'product:updated';
    case DELETED = 'product:deleted';
}
