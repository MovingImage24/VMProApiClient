<?php

namespace MovingImage\Util\Logging\Traits;

use Psr\Log\LoggerInterface;

/**
 * Trait that abstracts implementing of LoggerAwareInterface setter method
 * as well as logger storage + retrieval within a class.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Method to inject PSR logger into this class.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger instance associated with this instance.
     *
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }
}
