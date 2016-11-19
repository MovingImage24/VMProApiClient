<?php

namespace MovingImage\Client\VMPro\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use MovingImage\Client\VMPro\ApiClient\Guzzle5ApiClient;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Client\VMPro\Subscriber\TokenSubscriber;

/**
 * Class Guzzle5.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class Guzzle5ApiClientFactory extends AbstractApiClientFactory
{
    /**
     * Use the Guzzle5-specific API client class.
     *
     * @return string
     */
    protected function getApiClientClass()
    {
        return Guzzle5ApiClient::class;
    }

    protected function getGuzzleBaseUriOptionKey()
    {
        return 'base_url';
    }

    /**
     * Instantiate a TokenSubscriber instance with a TokenManager.
     *
     * @param TokenManager $tokenManager
     *
     * @return TokenSubscriber
     */
    public function createTokenSubscriber(TokenManager $tokenManager)
    {
        return new TokenSubscriber($tokenManager);
    }

    /**
     * Method to instantiate a HTTP client.
     *
     * @param string $baseUri
     * @param array  $subscribers
     * @param array  $options
     *
     * @return ClientInterface
     */
    public function createHttpClient($baseUri, array $subscribers = [], array $options = [])
    {
        return new Client(array_merge([
            'base_url' => $baseUri,
            'defaults' => [
                'subscribers' => $subscribers,
            ],
        ], $options));
    }
}
