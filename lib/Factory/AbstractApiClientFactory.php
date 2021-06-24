<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Interfaces\ApiClientFactoryInterface;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Client\VMPro\Manager\TokenManager;
use Psr\Log\LoggerInterface;

abstract class AbstractApiClientFactory implements ApiClientFactoryInterface
{
    /**
     * Get the API client class within Guzzle-client specific factories.
     */
    abstract protected function getApiClientClass(): string;

    /**
     * Get the Base URI Guzzle option key - for some reason Guzzle decided.
     */
    abstract protected function getGuzzleBaseUriOptionKey(): string;

    public function createTokenManager(
        string $baseUri,
        ApiCredentials $credentials
    ): TokenManager {
        return new TokenManager(
            new Client([$this->getGuzzleBaseUriOptionKey() => $baseUri]),
            $credentials,
            new TokenExtractor()
        );
    }

    public function createSerializer(): SerializerInterface
    {
        // Set up that JMS annotations can be loaded through autoloader
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        return SerializerBuilder::create()->build();
    }

    public function create(
        ClientInterface $httpClient,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ): ApiClientInterface {
        $cls = $this->getApiClientClass();
        $apiClient = new $cls($httpClient, $serializer);


        if (!is_null($logger)) {
            $apiClient->setLogger($logger);
        }

        return $apiClient;
    }

    abstract public function createSimple(string $baseUri, ApiCredentials $credentials, string $authUrl): ApiClientInterface;
}
