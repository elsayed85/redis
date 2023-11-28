<?php

namespace Elsayed85\LmsRedis\Utils;

use Elsayed85\LmsRedis\LmsRedis;
use ReflectionClass;

class Service
{
    public static function getAllServices(): array
    {
        $classes = [];
        $dir = __DIR__.'/../Services';
        $files = glob($dir.'/*/*.php');
        $parentClass = new ReflectionClass(LmsRedis::class);
        $namespace = 'Elsayed85\\LmsRedis\Services\\';
        foreach ($files as $file) {
            $folder = str_replace($dir.'/', '', dirname($file));
            $file = pathinfo($file, PATHINFO_FILENAME);
            $file = $namespace.$folder.'\\'.$file;
            if (class_exists($file)) {
                $reflectionClass = new ReflectionClass($file);
                if ($reflectionClass->isSubclassOf($parentClass)) {
                    $classes[] = $file;
                }
            }
        }

        return $classes;
    }
}
