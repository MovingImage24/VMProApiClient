<?php

namespace MovingImage\Client\VMPro\Interfaces;

/**
 * Defines an interface for making measurements of code execution time.
 */
interface StopwatchInterface
{
    /**
     * Starts the stopwatch for the specified segment (optionally allows specifying a category in second arg).
     *
     * @param string      $name
     * @param string|null $category
     */
    public function start($name, $category = null);

    /**
     * Stops the stopwatch for the specified segment.
     *
     * @param string $name
     */
    public function stop($name);
}
