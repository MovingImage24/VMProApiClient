<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Middleware;

use Closure;
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
    protected const AUTH_BEARER = 'Bearer %s';

    /**
     * @var TokenManager
     */
    private $tokenManager;

    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * Middleware invocation method that actually adds the bearer
     * token to the HTTP request.
     */
    public function __invoke(callable $handler): Closure
    {
        return function (
            RequestInterface $request,
            array $options
        ) use ($handler) {
            $videoManagerId = $options['videoManagerId'] ?? null;
            $token = $this->tokenManager->getToken($videoManagerId);

            return $handler($request->withHeader(
                'Authorization',
                sprintf(self::AUTH_BEARER, $token)
            ), $options);
        };
    }
}
