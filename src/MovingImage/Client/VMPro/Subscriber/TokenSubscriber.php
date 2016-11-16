<?php

namespace MovingImage\Client\VMPro\Subscriber;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

class TokenSubscriber implements SubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $tokenManager;

    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function getEvents()
    {
        return [
            'before' => ['onBefore', RequestEvents::SIGN_REQUEST],
        ];
    }

    /**
     * Add the Authorization header to requests.
     *
     * @param BeforeEvent $event Event received
     */
    public function onBefore(BeforeEvent $event)
    {
        $request = $event->getRequest();
        $options = $request->getConfig();

        $videoManagerId = isset($options['videoManagerId']) ? $options['videoManagerId'] : null;
        $token = $this->tokenManager->getToken($videoManagerId);

        if ($token !== null) {
            $request->setHeader('Authorization', 'Bearer ' . $token);
        }
    }
}
