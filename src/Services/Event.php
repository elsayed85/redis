<?php

namespace Elsayed85\LmsRedis\Services;

abstract class Event
{
    public function toJson(): string
    {
        return json_encode($this);
    }
}
