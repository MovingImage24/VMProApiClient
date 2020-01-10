<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use MovingImage\Client\VMPro\ApiClient;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Client\VMPro\Middleware\TokenMiddleware;

class Guzzle6ApiClientFactory extends AbstractApiClientFactory
{
    /**
     * Use the Guzzle6-specific API client class.
     */
    protected function getApiClientClass(): string
    {
        return ApiClient::class;
    }

    protected function getGuzzleBaseUriOptionKey(): string
    {
        return 'base_uri';
    }

    /**
     * Instantiate a TokenMiddleware instance with a TokenManager.
     */
    public function createTokenMiddleware(TokenManager $tokenManager): TokenMiddleware
    {
        return new TokenMiddleware($tokenManager);
    }

    /**
     * Method to instantiate a HTTP client.
     */
    public function createHttpClient(string $baseUri, ?array $middlewares, ?array $options = []): ClientInterface
    {
        $middlewares = $middlewares ?? [];
        $options = $options ?? [];

        $stack = HandlerStack::create();

        foreach ($middlewares as $middleware) {
            $stack->push($middleware);
        }

        return new Client(array_merge([
            'base_uri' => $baseUri,
            'handler' => $stack,
        ], $options));
    }

    public function createSimple($baseUri, ApiCredentials $credentials, $authUrl): ApiClientInterface
    {
        $tokenManager = $this->createTokenManager($authUrl, $credentials);
        $tokenMiddleware = $this->createTokenMiddleware($tokenManager);
        $httpClient = $this->createHttpClient($baseUri, [$tokenMiddleware]);

        return $this->create($httpClient, $this->createSerializer());
    }
}
