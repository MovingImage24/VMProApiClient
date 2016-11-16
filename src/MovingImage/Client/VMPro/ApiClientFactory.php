<?php

namespace MovingImage\Client\VMPro;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use JMS\Serializer\SerializerBuilder;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Client\VMPro\Middleware\TokenMiddleware;
use Psr\Log\LoggerInterface;

/**
 * Class ApiClientFactory.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class ApiClientFactory
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
            new Client(['base_uri' => $baseUri]),
            $credentials,
            new TokenExtractor()
        );
    }

    /**
     * Instantiate a TokenMiddleware instance with a TokenManager.
     *
     * @param TokenManager $tokenManager
     *
     * @return TokenMiddleware
     */
    protected function createTokenMiddleware(TokenManager $tokenManager)
    {
        return new TokenMiddleware($tokenManager);
    }

    /**
     * Method to instantiate a HTTP client.
     *
     * @param string          $baseUri
     * @param TokenMiddleware $tokenMiddleware
     * @param array           $options
     *
     * @return ClientInterface
     */
    protected function createHttpClient($baseUri, TokenMiddleware $tokenMiddleware, array $options = [])
    {
        $stack = HandlerStack::create();
        $stack->push($tokenMiddleware);

        return new Client(array_merge([
            'base_uri' => $baseUri,
            'handler' => $stack,
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
     * @return ApiClient
     */
    public function create($baseUri, ApiCredentials $credentials, array $options = [], $logger = null)
    {
        if (is_null($logger)) {
            $logger = new Logger('VMPro API Client');
            $logger->pushHandler(new NullHandler());
        }

        $tokenManager = $this->createTokenManager($baseUri, $credentials);
        $tokenManager->setLogger($logger);

        $tokenMiddleware = $this->createTokenMiddleware($tokenManager);
        $tokenMiddleware->setLogger($logger);

        $httpClient = $this->createHttpClient($baseUri, $tokenMiddleware, $options);

        $apiClient = new ApiClient($httpClient, $this->createSerializer());
        $apiClient->setLogger($logger);

        return $apiClient;
    }
}
