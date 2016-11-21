<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\ApiClient\Guzzle5ApiClient;

class Guzzle5ApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var History
     */
    private $history;

    private function createJsonStream($arr)
    {
        $str = json_encode($arr);
        $stream = Stream::factory($str);

        return $stream;
    }

    public function setUp()
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '>=')) {
            $this->markTestSkipped('Skipping tests for Guzzle5ApiClient when Guzzle ~6.0 is installed');
        }

        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        $this->history = new History();
        $this->httpClient = new Client();
        $this->httpClient->getEmitter()->attach($this->history);
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function testDoRequest()
    {
        $mock = new Mock([
            new Response(200, ['X-Foo' => 'Bar'], $this->createJsonStream([
                'id' => 5,
                'name' => 'root_channel',
            ])),
        ]);

        $this->httpClient->getEmitter()->attach($mock);

        $client = new Guzzle5ApiClient($this->httpClient, $this->serializer);
        $client->getChannels(5);

        $request = $this->history->getLastRequest();

        $this->assertCount(1, $this->history);
        $this->assertEquals(5, $request->getConfig()['videoManagerId']);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('5/channels', $request->getUrl());
    }
}
