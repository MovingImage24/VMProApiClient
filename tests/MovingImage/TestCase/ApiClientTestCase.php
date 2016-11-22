<?php

namespace MovingImage\TestCase;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\ApiClient\Guzzle5ApiClient;
use MovingImage\Client\VMPro\ApiClient\Guzzle6ApiClient;

/**
 * Class ApiClientTestCase.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class ApiClientTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var mixed Contains the mock client history stack
     */
    private $historyStack;

    /**
     * Create a stream from a JSON string (using Guzzle 5 helpers).
     *
     * @param array $arr
     *
     * @return mixed
     */
    private function createGuzzle5JsonStream($arr)
    {
        $str = json_encode($arr);
        $stream = Stream::factory($str);

        return $stream;
    }

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
        $stream = \GuzzleHttp\Psr7\stream_for($str);

        return $stream;
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
        if (version_compare(ClientInterface::VERSION, '6.0', '<')) {
            $mock = new Mock([
                new \GuzzleHttp\Message\Response($statusCode, $headers, $this->createGuzzle5JsonStream($bodyJson)),
            ]);

            $this->historyStack = new History();

            $httpClient = new Client();
            $httpClient->getEmitter()->attach($mock);
            $httpClient->getEmitter()->attach($this->historyStack);

            return $httpClient;
        } else {
            $mock = new MockHandler([
                new \GuzzleHttp\Psr7\Response($statusCode, $headers, $this->createGuzzle6JsonStream($bodyJson)),
            ]);

            $this->historyStack = [];
            $history = Middleware::history($this->historyStack);

            $stack = HandlerStack::create($mock);
            $stack->push($history);

            return new Client([
                'handler' => $stack,
            ]);
        }
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    protected function createSerializer()
    {
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        return SerializerBuilder::create()->build();
    }

    /**
     * Create an API client instance based on which Guzzle version
     * is installed into the project requirements.
     *
     * @param ClientInterface $httpClient
     * @param Serializer      $serializer
     *
     * @return Guzzle5ApiClient|Guzzle6ApiClient
     */
    protected function createApiClient($httpClient, $serializer)
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '<')) {
            return new Guzzle5ApiClient($httpClient, $serializer);
        } else {
            return new Guzzle6ApiClient($httpClient, $serializer);
        }
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
}
