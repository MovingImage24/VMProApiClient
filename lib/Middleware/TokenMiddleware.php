<?php

namespace MovingImage\Client\VMPro\Middleware;

use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Client\VMPro\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerAwareInterface;

class TokenMiddleware implements LoggerAwareInterface
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
     * TokenMiddleware constructor.
     *
     * @param TokenManager $tokenManager
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * Middleware invocation method that actually adds the bearer
     * token to the HTTP request.
     *
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (
            RequestInterface $request,
            array $options
        ) use ($handler) {
            $videoManagerId = isset($options['videoManagerId']) ? $options['videoManagerId'] : null;
            $token = $this->tokenManager->getToken($videoManagerId);

            return $handler($request->withHeader(
                'Authorization',
                sprintf(self::AUTH_BEARER, $token)
            ), $options);
        };
    }
}
