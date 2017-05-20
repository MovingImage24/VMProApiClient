<?php

namespace MovingImage\Client\VMPro\Util\Logging\Traits;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
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
        if (!isset($this->logger)) {
            // When no logger is injected, create a new one
            // that doesn't do anything
            $this->logger = new Logger('api-client');
            $this->logger->setHandlers([
                new NullHandler(),
            ]);
        }

        return $this->logger;
    }
}
