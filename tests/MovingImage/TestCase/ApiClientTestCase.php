<?php

declare(strict_types=1);

namespace MovingImage\TestCase;

use Doctrine\Common\Annotations\AnnotationRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use MovingImage\Client\VMPro\ApiClient\Guzzle6ApiClient;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class ApiClientTestCase extends TestCase
{
    /**
     * @var mixed Contains the mock client history stack
     */
    private $historyStack;

    /**
     * Create a stream from a JSON string (using Guzzle 6 helpers).
     *
     * @param array $arr
     *
     * @return mixed
     */
    private function createGuzzle6JsonStream($arr)
    {
        $str = json_encode($arr);

        return stream_for($str);
    }

    /**
     * Create a Guzzle client with mock data.
     *
     * @param $statusCode
     * @param array $headers
     * @param array $bodyJson
     *
     * @return ClientInterface
     */
    protected function createMockGuzzleClient($statusCode, $headers = [], $bodyJson = [])
    {
        $mock = new MockHandler([
            new Response($statusCode, $headers, $this->createGuzzle6JsonStream($bodyJson)),
        ]);

        $this->historyStack = [];
        $history = Middleware::history($this->historyStack);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        return new Client([
            'handler' => $stack,
        ]);
    }

    /**
     * @return SerializerInterface
     */
    protected function createSerializer()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $serializerBuilder = SerializerBuilder::create();

        return $serializerBuilder->build();
    }

    /**
     * Create an API client instance based on which Guzzle version
     * is installed into the project requirements.
     *
     * @param ClientInterface $httpClient
     * @param Serializer      $serializer
     *
     * @return Guzzle6ApiClient
     */
    protected function createApiClient($httpClient, $serializer)
    {
        return new Guzzle6ApiClient($httpClient, $serializer);
    }

    /**
     * Get the history stack for doing assertions.
     *
     * @return mixed
     */
    protected function getHistoryStack()
    {
        return $this->historyStack;
    }

    /**
     * Get last performed request's Request object.
     *
     * @return object
     */
    protected function getLastRequest()
    {
        return $this->getHistoryStack()[0]['request'];
    }
}
