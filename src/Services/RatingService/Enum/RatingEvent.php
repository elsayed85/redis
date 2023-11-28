<?php

namespace Elsayed85\LmsRedis\Services\RatingService\Enum;

enum RatingEvent: string
{
    case CREATED = 'rating:created';
    case UPDATED = 'rating:updated';
    case DELETED = 'rating:deleted';
}
