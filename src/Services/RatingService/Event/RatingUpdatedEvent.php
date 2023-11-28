<?php

namespace Elsayed85\LmsRedis\Services\RatingService\Event;

use Elsayed85\LmsRedis\Services\Event;
use Elsayed85\LmsRedis\Services\RatingService\DTO\RatingData;
use Elsayed85\LmsRedis\Services\RatingService\Enum\RatingEvent;

class RatingUpdatedEvent extends Event
{
    public RatingEvent $type = RatingEvent::UPDATED;

    public function __construct(public readonly RatingData $data)
    {
    }
}
