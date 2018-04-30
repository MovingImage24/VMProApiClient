<?php

namespace MovingImage\Client\VMPro\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Interfaces\ApiClientFactoryInterface;
use MovingImage\Client\VMPro\Interfaces\StopwatchInterface;
use MovingImage\Client\VMPro\Manager\TokenManager;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractApiClientFactory implements ApiClientFactoryInterface
{
    /**
     * Get the API client class within Guzzle-client specific factories.
     *
     * @return string
     */
    abstract protected function getApiClientClass();

    /**
     * Get the Base URI Guzzle option key - for some reason Guzzle decided
     * to change it between ~5.0 and ~6.0..
     *
     * @return string
     */
    abstract protected function getGuzzleBaseUriOptionKey();

    /**
     * {@inheritdoc}
     */
    public function createTokenManager(
        $baseUri,
        ApiCredentials $credentials,
        CacheItemPoolInterface $cacheItemPool = null
    ) {
        return new TokenManager(
            new Client([$this->getGuzzleBaseUriOptionKey() => $baseUri]),
            $credentials,
            new TokenExtractor(),
            $cacheItemPool
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createSerializer()
    {
        // Set up that JMS annotations can be loaded through autoloader
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        return SerializerBuilder::create()->build();
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        ClientInterface $httpClient,
        Serializer $serializer,
        LoggerInterface $logger = null,
        CacheItemPoolInterface $cacheItemPool = null,
        $cacheTtl = null,
        StopwatchInterface $stopwatch = null
    ) {
        $cls = $this->getApiClientClass();
        $apiClient = new $cls($httpClient, $serializer, $cacheItemPool, $cacheTtl, $stopwatch);

        if (!is_null($logger)) {
            $apiClient->setLogger($logger);
        }

        return $apiClient;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function createSimple($baseUri, ApiCredentials $credentials, $authUrl);
}
