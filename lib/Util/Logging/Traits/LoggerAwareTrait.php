<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Util\Logging\Traits;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Trait that abstracts implementing of LoggerAwareInterface setter method
 * as well as logger storage + retrieval within a class.
 */
trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Method to inject PSR logger into this class.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger instance associated with this instance.
     */
    protected function getLogger(): LoggerInterface
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
