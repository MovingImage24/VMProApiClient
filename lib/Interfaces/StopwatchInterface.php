<?php

namespace MovingImage\Client\VMPro\Interfaces;

/**
 * Defines an interface for making measurements of code execution time.
 */
interface StopwatchInterface
{
    /**
     * Starts the stopwatch for the specified segment (optionally allows specifying a category in second arg).
     */
    public function start(string $name, ?string $category = null): void;

    /**
     * Stops the stopwatch for the specified segment.
     */
    public function stop(string $name): void;
}
