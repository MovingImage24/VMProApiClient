<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractCoreApiClientTest.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class AbstractCoreApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractApiClientImpl
     */
    private $client;

    /**
     * Set up an instance of our mock abstract api client implementation.
     */
    public function setUp()
    {
        $this->client = new AbstractApiClientImpl(new Client(), SerializerBuilder::create()->build());
    }

    /**
     * Assert whether required parameters pass through the function
     * and whether their value is still correct.
     */
    public function testBuildJsonParametersSuccessRequiredParams()
    {
        $res = $this->client->buildJsonParameters([
            'test' => 'bla',
        ], []);

        $this->assertArrayHasKey('test', $res);
        $this->assertEquals('bla', $res['test']);
    }

    /**
     * Assert whether an exception is thrown when a required parameter
     * has an empty value.
     *
     * @expectedException \Exception
     */
    public function testBuildJsonParametersFailedMissingRequiredParams()
    {
        $this->client->buildJsonParameters([
            'test' => '',
        ], []);
    }

    /**
     * Assert whether optional parameters are appropriately passed through
     * when they do have values, or omitted when they don't, and whether
     * their values are still correct.
     */
    public function testBuildJsonParametersSuccessOptionalParameters()
    {
        $res = $this->client->buildJsonParameters([
            'test' => 'bla',
        ], [
            'should' => 'test',
            'should_not' => '',
            'should_not_either' => null,
        ]);

        $this->assertArrayHasKey('should', $res);
        $this->assertArrayNotHasKey('should_not', $res);
        $this->assertArrayNotHasKey('should_not_either', $res);

        $this->assertEquals('test', $res['should']);
    }

    /**
     * Asserts that makeRequest method will return the cached response
     * if it exists in cache.
     */
    public function testCachedResponse()
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(Serializer::class);
        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $statusCode = 200;
        $headers = ['Content-Type' => ['application/json']];
        $body = 'test';
        $cachedResponse = new Response($statusCode, $headers, $body);

        $cacheItem->method('get')->willReturn($cachedResponse);
        $cacheItem->method('isHit')->willReturn(true);
        $cachePool->method('getItem')->willReturn($cacheItem);
        $client = new AbstractApiClientImpl($httpClient, $serializer, $cachePool);

        $rc = new \ReflectionClass($client);
        $method = $rc->getMethod('makeRequest');
        $method->setAccessible(true);

        /** @var ResponseInterface $response */
        $response = $method->invoke($client, 'GET', 'http://example.org', []);

        $this->assertSame($statusCode, $response->getStatusCode());
        $this->assertSame($headers, $response->getHeaders());
        $this->assertSame($body, $response->getBody()->getContents());
        $response->getBody()->rewind();
    }

    /**
     * Asserts that makeRequest will return the response when it is not cached.
     */
    public function testUncachedResponse()
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(Serializer::class);
        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $expectedResponse = new Response();
        $cacheItem->method('isHit')->willReturn(false);
        $cachePool->method('getItem')->willReturn($cacheItem);
        $client = new AbstractApiClientImpl($httpClient, $serializer, $cachePool);
        $client->setResponse($expectedResponse);

        $rc = new \ReflectionClass($client);
        $method = $rc->getMethod('makeRequest');
        $method->setAccessible(true);

        $response = $method->invoke($client, 'GET', 'http://example.org', []);
        $this->assertSame($expectedResponse, $response);
    }

    /**
     * Asserts that makeRequest will return a response when no cache implementation is provided.
     */
    public function testNullCache()
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(Serializer::class);
        $expectedResponse = new Response();
        $client = new AbstractApiClientImpl($httpClient, $serializer);
        $client->setResponse($expectedResponse);

        $rc = new \ReflectionClass($client);
        $method = $rc->getMethod('makeRequest');
        $method->setAccessible(true);

        $response = $method->invoke($client, 'GET', 'http://example.org', []);

        $this->assertSame($expectedResponse, $response);
    }
}
