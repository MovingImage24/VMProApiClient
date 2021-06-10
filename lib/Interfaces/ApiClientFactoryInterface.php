<?php

namespace MovingImage\Client\VMPro\Interfaces;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerInterface;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Manager\TokenManager;
use Psr\Log\LoggerInterface;

interface ApiClientFactoryInterface
{
    public const VERSION = '0.2';

    /**
     * Instantiate a TokenManager with a set of API credentials.
     */
    public function createTokenManager(
        string $baseUri,
        ApiCredentials $credentials
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
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ): ApiClientInterface;

    /**
     * Abstraction to more simpler instantiate an API client.
     *
     * @return ApiClientInterface
     */
    public function createSimple(string $baseUri, ApiCredentials $credentials, string $authUrl): ApiClientInterface;
}
