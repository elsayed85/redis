<?php

namespace Elsayed85\LmsRedis\Traits;

trait HasEvents
{
    public static function events()
    {
        $dir = (new \ReflectionClass(static::class))->getFileName();
        $dir = dirname($dir).'/Event';

        $files = glob($dir.'/*.php');

        $namespace = (new \ReflectionClass(static::class))->getNamespaceName().'\\Event';

        $classes = array_map(function ($file) use ($namespace) {
            $file = $namespace.'\\'.pathinfo($file, PATHINFO_FILENAME);

            return class_exists($file) ? $file : null;
        }, $files);

        $events = array_filter($classes);

        $pramters = array_map(function ($event) {
            $reflection = new \ReflectionClass($event);
            $constructor = $reflection->getConstructor();
            $params = $constructor->getParameters();
            $params = array_map(function ($param) {
                return $param->getType()->getName();
            }, $params);

            return $params;
        }, $events);

        $events = array_combine($events, $pramters);

        return $events;
    }
}
