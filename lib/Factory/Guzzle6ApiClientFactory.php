<?php

namespace MovingImage\Client\VMPro\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use MovingImage\Client\VMPro\ApiClient;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Client\VMPro\Middleware\TokenMiddleware;

/**
 * Class ApiClientFactory.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class Guzzle6ApiClientFactory extends AbstractApiClientFactory
{
    /**
     * Use the Guzzle6-specific API client class.
     *
     * @return string
     */
    protected function getApiClientClass()
    {
        return ApiClient::class;
    }

    protected function getGuzzleBaseUriOptionKey()
    {
        return 'base_uri';
    }

    /**
     * Instantiate a TokenMiddleware instance with a TokenManager.
     *
     * @param TokenManager $tokenManager
     *
     * @return TokenMiddleware
     */
    public function createTokenMiddleware(TokenManager $tokenManager)
    {
        return new TokenMiddleware($tokenManager);
    }

    /**
     * Method to instantiate a HTTP client.
     *
     * @param string $baseUri
     * @param array  $middlewares
     * @param array  $options
     *
     * @return ClientInterface
     */
    public function createHttpClient($baseUri, array $middlewares = [], array $options = [])
    {
        $stack = HandlerStack::create();

        foreach ($middlewares as $middleware) {
            $stack->push($middleware);
        }

        return new Client(array_merge([
            'base_uri' => $baseUri,
            'handler' => $stack,
        ], $options));
    }

    /**
     * {@inheritdoc}
     */
    public function createSimple($baseUri, ApiCredentials $credentials)
    {
        $tokenManager = $this->createTokenManager($baseUri, $credentials);
        $tokenMiddleware = $this->createTokenMiddleware($tokenManager);
        $httpClient = $this->createHttpClient($baseUri, [$tokenMiddleware]);

        return $this->create($httpClient, $this->createSerializer());
    }
}
