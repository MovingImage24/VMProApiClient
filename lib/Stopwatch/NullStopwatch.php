<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Stopwatch;

use MovingImage\Client\VMPro\Interfaces\StopwatchInterface;

/**
 * A no-op implementation of the StopwatchInterface.
 */
class NullStopwatch implements StopwatchInterface
{
    public function start(string $name, ?string $category = null): void
    {
    }

    public function stop(string $name): void
    {
    }
}
