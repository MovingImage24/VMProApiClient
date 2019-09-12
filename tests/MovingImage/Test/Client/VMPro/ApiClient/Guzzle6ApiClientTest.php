<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\ApiClient\Guzzle6ApiClient;
use Psr\Http\Message\ResponseInterface;

class Guzzle6ApiClientTest extends \PHPUnit_Framework_TestCase
{
    private $httpClient;
    private $historyContainer = [];
    private $serializer;

    private function createJsonStream($arr)
    {
        $str = json_encode($arr);
        $stream = \GuzzleHttp\Psr7\stream_for($str);

        return $stream;
    }

    public function setUp()
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '<')) {
            $this->markTestSkipped('Skipping tests for Guzzle6ApiClient when Guzzle ~5.0 is installed');
        }

        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], $this->createJsonStream([
                'id' => 5,
                'name' => 'root_channel',
            ])),
        ]);
        $history = Middleware::history($this->historyContainer);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $this->httpClient = new Client([
            'handler' => $stack,
        ]);

        $this->serializer = SerializerBuilder::create()->build();
    }

    public function testDoRequest()
    {
        $client = new Guzzle6ApiClient($this->httpClient, $this->serializer);
        $client->getChannels(5);

        $this->assertCount(1, $this->historyContainer);
        $this->assertEquals('GET', $this->historyContainer[0]['request']->getMethod());
        $this->assertEquals('5/channels', $this->historyContainer[0]['request']->getUri());
    }

    /**
     * Tests both serializeResponse and unserializeResponse methods.
     */
    public function testSerializeResponse()
    {
        $status = 200;
        $headers = ['Content-Type' => ['application/json']];
        $body = 'test';
        $response = new Response(200, $headers, $body);

        $httpClient = $this->createMock(ClientInterface::class);
        $client = new Guzzle6ApiClient($httpClient, $this->serializer);

        $rc = new \ReflectionClass($client);
        $serializeMethod = $rc->getMethod('serializeResponse');
        $serializeMethod->setAccessible(true);
        $serialized = $serializeMethod->invoke($client, $response);

        //after serializing, original response must not be modified!
        $this->assertSame($status, $response->getStatusCode());
        $this->assertSame($headers, $response->getHeaders());
        $this->assertSame($body, $response->getBody()->getContents());

        $this->assertInternalType('string', $serialized);

        $unserializeMethod = $rc->getMethod('unserializeResponse');
        $unserializeMethod->setAccessible(true);
        /** @var ResponseInterface $unserialized */
        $unserialized = $unserializeMethod->invoke($client, $serialized);

        $this->assertInstanceOf(ResponseInterface::class, $unserialized);

        $this->assertSame($status, $unserialized->getStatusCode());
        $this->assertSame($headers, $unserialized->getHeaders());
        $this->assertSame($body, $unserialized->getBody()->getContents());
    }
}
