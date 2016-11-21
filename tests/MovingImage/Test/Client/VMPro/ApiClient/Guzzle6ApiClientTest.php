<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\ApiClient\Guzzle6ApiClient;

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
}
