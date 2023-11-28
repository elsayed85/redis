<?php

namespace Elsayed85\LmsRedis\Services\RatingService\Event;

use Elsayed85\LmsRedis\Services\Event;
use Elsayed85\LmsRedis\Services\RatingService\DTO\RatingData;
use Elsayed85\LmsRedis\Services\RatingService\Enum\RatingEvent;

class RatingCreatedEvent extends Event
{
    public RatingEvent $type = RatingEvent::CREATED;

    public function __construct(public readonly RatingData $data)
    {
    }
}
