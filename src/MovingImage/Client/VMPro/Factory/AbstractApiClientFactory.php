<?php

namespace MovingImage\Client\VMPro\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Client\VMPro\Manager\TokenManager;
use Psr\Log\LoggerInterface;

abstract class AbstractApiClientFactory
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
     * Instantiate a TokenManager with a set of API credentials.
     *
     * @param string         $baseUri
     * @param ApiCredentials $credentials
     *
     * @return TokenManager
     */
    public function createTokenManager($baseUri, ApiCredentials $credentials)
    {
        return new TokenManager(
            new Client([$this->getGuzzleBaseUriOptionKey() => $baseUri]),
            $credentials,
            new TokenExtractor()
        );
    }

    /**
     * Method to instantiate a serializer instance.
     *
     * @return \JMS\Serializer\Serializer
     */
    public function createSerializer()
    {
        // Set up that JMS annotations can be loaded through autoloader
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        return SerializerBuilder::create()->build();
    }

    /**
     * Factory method to create a new instance of the VMPro
     * API Client.
     *
     * @param ClientInterface      $httpClient
     * @param Serializer           $serializer
     * @param LoggerInterface|null $logger
     *
     * @return ApiClientInterface
     */
    public function create(
        ClientInterface $httpClient,
        Serializer $serializer,
        $logger = null
    ) {
        $cls = $this->getApiClientClass();
        $apiClient = new $cls($httpClient, $serializer);

        if (!is_null($logger)) {
            $apiClient->setLogger($logger);
        }

        return $apiClient;
    }
}
