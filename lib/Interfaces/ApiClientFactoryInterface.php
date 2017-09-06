<?php

namespace MovingImage\Client\VMPro\Interfaces;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Manager\TokenManager;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface ApiClientFactoryInterface.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
interface ApiClientFactoryInterface
{
    const VERSION = '0.2';

    /**
     * Instantiate a TokenManager with a set of API credentials.
     *
     * @param string         $baseUri
     * @param ApiCredentials $credentials
     *
     * @return TokenManager
     */
    public function createTokenManager($baseUri, ApiCredentials $credentials);

    /**
     * Method to instantiate a serializer instance.
     *
     * @return \JMS\Serializer\Serializer
     */
    public function createSerializer();

    /**
     * Factory method to create a new instance of the VMPro
     * API Client.
     *
     * @param ClientInterface        $httpClient
     * @param Serializer             $serializer
     * @param LoggerInterface|null   $logger
     * @param CacheItemPoolInterface $cacheItemPool
     * @param mixed                  $cacheTtl
     *
     * @return ApiClientInterface
     */
    public function create(
        ClientInterface $httpClient,
        Serializer $serializer,
        LoggerInterface $logger = null,
        CacheItemPoolInterface $cacheItemPool = null,
        $cacheTtl = null
    );

    /**
     * Abstraction to more simpler instantiate an API client.
     *
     * @param string         $baseUri
     * @param ApiCredentials $credentials
     *
     * @return mixed
     */
    public function createSimple($baseUri, ApiCredentials $credentials);
}
