<?php

namespace Elsayed85\LmsRedis;

use Carbon\Carbon;
use Elsayed85\LmsRedis\Facades\Redis;
use Elsayed85\LmsRedis\Services\Event;
use Elsayed85\LmsRedis\Utils\Enum;

abstract class LmsRedis
{
    protected string $allEventsKey;

    protected string $processedEventsKey;

    public function __construct()
    {
        $this->allEventsKey = 'events';
        $this->processedEventsKey = 'processed_events';
    }

    abstract public function getServiceName(): string;

    private function getProcessedEventKey(): string
    {
        return $this->getServiceName().'-'.$this->processedEventsKey;
    }

    public function publish(Event $event): void
    {
        Redis::xadd($this->allEventsKey, '*', [
            'event' => $event->toJson(),
            'service' => $this->getServiceName(),
            'created_at' => Carbon::now()->valueOf(),
        ]);
    }

    public function addProcessedEvent(array $event): void
    {
        Redis::rpush(
            $this->getProcessedEventKey(),
            $event['id']
        );
    }

    public function getUnProcessedEvents(): array
    {
        $lastProcessedEventId = $this->getLastProcessedEventId(); // [timestamp]

        if (empty($lastProcessedEventId)) {
            return [];
        }

        $events = $this->getEventsAfter($lastProcessedEventId);

        return $this->parseEvents($events);
    }

    private function getLastProcessedEventId(): ?string
    {
        $lastId = Redis::lindex($this->getProcessedEventKey(), -1);

        if (empty($lastId)) {
            return (string) Carbon::now()->subYears(10)->valueOf();
        }

        return $lastId;
    }

    protected function getEventsAfter(string $start): array
    {
        $events = Redis::xRange(
            $this->allEventsKey,
            $start,
            (int) Carbon::now()->valueOf()
        );

        if (! $events) {
            return [];
        }

        unset($events[$start]); // remove start because it's already processed

        return $events;
    }

    protected function parseEvents(array $redisEvents): array
    {
        return collect($redisEvents)
            ->map(function (array $item, string $id) {
                $event = array_merge(
                    json_decode($item['event'], true),
                    ['id' => $id]
                );
                $event['type'] = Enum::From($event['type']);

                return $event;
            })->all();
    }
}
