<?php

namespace MovingImage\Client\VMPro\Interfaces;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Manager\TokenManager;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

interface ApiClientFactoryInterface
{
    public const VERSION = '0.2';

    /**
     * Instantiate a TokenManager with a set of API credentials.
     * If CacheItemPoolInterface implementation is provided,
     * it will be used to cache the API token.
     */
    public function createTokenManager(
        string $baseUri,
        ApiCredentials $credentials,
        ?CacheItemPoolInterface $cacheItemPool = null
    ): TokenManager;

    /**
     * Method to instantiate a serializer instance.
     */
    public function createSerializer(): SerializerInterface;

    /**
     * Factory method to create a new instance of the VMPro
     * API Client.
     */
    public function create(
        ClientInterface $httpClient,
        Serializer $serializer,
        ?LoggerInterface $logger = null,
        ?CacheItemPoolInterface $cacheItemPool = null,
        ?int $cacheTtl = null,
        ?StopwatchInterface $stopwatch = null
    ): ApiClientInterface;

    /**
     * Abstraction to more simpler instantiate an API client.
     *
     * @return mixed
     */
    public function createSimple(string $baseUri, ApiCredentials $credentials, string $authUrl);
}
