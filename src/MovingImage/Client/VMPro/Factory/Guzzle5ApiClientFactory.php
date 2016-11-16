<?php

namespace MovingImage\Client\VMPro\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerBuilder;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\ApiClient\Guzzle5ApiClient;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Client\VMPro\Subscriber\TokenSubscriber;
use Psr\Log\LoggerInterface;

/**
 * Class Guzzle5.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class Guzzle5ApiClientFactory
{
    /**
     * Instantiate a TokenManager with a set of API credentials.
     *
     * @param string         $baseUri
     * @param ApiCredentials $credentials
     *
     * @return TokenManager
     */
    protected function createTokenManager($baseUri, ApiCredentials $credentials)
    {
        return new TokenManager(
            new Client(['base_url' => $baseUri]),
            $credentials,
            new TokenExtractor()
        );
    }

    /**
     * Instantiate a TokenSubscriber instance with a TokenManager.
     *
     * @param TokenManager $tokenManager
     *
     * @return TokenSubscriber
     */
    protected function createTokenSubscriber(TokenManager $tokenManager)
    {
        return new TokenSubscriber($tokenManager);
    }

    /**
     * Method to instantiate a HTTP client.
     *
     * @param string          $baseUri
     * @param TokenSubscriber $tokenSubscriber
     * @param array           $options
     *
     * @return ClientInterface
     */
    protected function createHttpClient($baseUri, TokenSubscriber $tokenSubscriber, array $options = [])
    {
        return new Client(array_merge([
            'base_url' => $baseUri,
            'defaults' => [
                'subscribers' => [$tokenSubscriber],
            ],
        ], $options));
    }

    /**
     * Method to instantiate a serializer instance.
     *
     * @return \JMS\Serializer\Serializer
     */
    protected function createSerializer()
    {
        // Set up that JMS annotations can be loaded through autoloader
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        return SerializerBuilder::create()->build();
    }

    /**
     * Factory method to create a new instance of the VMPro
     * API Client.
     *
     * @param string          $baseUri
     * @param ApiCredentials  $credentials
     * @param array           $options
     * @param LoggerInterface $logger
     *
     * @return Guzzle5ApiClient
     */
    public function create($baseUri, ApiCredentials $credentials, array $options = [], $logger = null)
    {
        if (is_null($logger)) {
            $logger = new Logger('VMPro API Client');
            $logger->pushHandler(new NullHandler());
        }

        $tokenManager = $this->createTokenManager($baseUri, $credentials);
        $tokenManager->setLogger($logger);

        $tokenSubscriber = $this->createTokenSubscriber($tokenManager);
        $tokenSubscriber->setLogger($logger);

        $httpClient = $this->createHttpClient($baseUri, $tokenSubscriber, $options);

        $apiClient = new Guzzle5ApiClient($httpClient, $this->createSerializer());
        $apiClient->setLogger($logger);

        return $apiClient;
    }
}

