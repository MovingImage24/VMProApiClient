<?php

namespace MovingImage\Client\VMPro\Subscriber;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Class TokenSubscriber.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class TokenSubscriber implements SubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @const string
     */
    const AUTH_BEARER = 'Bearer %s';

    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * TokenSubscriber constructor.
     *
     * @param TokenManager $tokenManager
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * {@inheritdoc}
     */
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
            $request->setHeader('Authorization', sprintf(self::AUTH_BEARER, $token));
        }
    }
}
