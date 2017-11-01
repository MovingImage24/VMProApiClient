<?php

namespace MovingImage\Client\VMPro\Stopwatch;

use MovingImage\Client\VMPro\Interfaces\StopwatchInterface;

/**
 * A no-op implementation of the StopwatchInterface.
 */
class NullStopwatch implements StopwatchInterface
{
    /**
     * {@inheritdoc}
     */
    public function start($name, $category = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function stop($name)
    {
    }
}
