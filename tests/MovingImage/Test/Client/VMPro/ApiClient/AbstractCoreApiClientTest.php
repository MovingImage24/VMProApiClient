<?php

declare(strict_types=1);

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use MovingImage\VMPro\TestUtil\GuzzleResponseGenerator;
use MovingImage\VMPro\TestUtil\PrivateMethodCaller;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;

class AbstractCoreApiClientTest extends TestCase
{
    use PrivateMethodCaller;
    use GuzzleResponseGenerator;

    /**
     * @var AbstractApiClientImpl
     */
    private $client;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Set up an instance of our mock abstract api client implementation.
     */
    public function setUp(): void
    {
        $this->client = new AbstractApiClientImpl(new Client(), SerializerBuilder::create()->build());
        $this->serializer = SerializerBuilder::create()->build();
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
     */
    public function testBuildJsonParametersFailedMissingRequiredParams()
    {
        $this->expectException(\Exception::class);

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
        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $statusCode = 200;
        $headers = ['Content-Type' => ['application/json']];
        $body = 'test';
        $cachedResponse = $this->generateGuzzleResponse($statusCode, $headers, $body);

        $cacheItem->method('get')->willReturn($cachedResponse);
        $cacheItem->method('isHit')->willReturn(true);
        $cachePool->method('getItem')->willReturn($cacheItem);
        $client = new AbstractApiClientImpl($httpClient, $this->serializer, $cachePool);

        /** @var ResponseInterface $response */
        $response = $this->callMethod($client, 'makeRequest', ['GET', 'http://example.org', []]);

        $this->assertSame($statusCode, $response->getStatusCode());
        $this->assertSame($headers, $response->getHeaders());
        $this->assertSame($body, $response->getBody()->getContents());
    }

    /**
     * Asserts that makeRequest will return the response when it is not cached.
     */
    public function testUncachedResponse()
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $expectedResponse = $this->generateGuzzleResponse();
        $cacheItem->method('isHit')->willReturn(false);
        $cachePool->method('getItem')->willReturn($cacheItem);
        $client = new AbstractApiClientImpl($httpClient, $this->serializer, $cachePool);
        $client->setResponse($expectedResponse);

        /** @var ResponseInterface $response */
        $response = $this->callMethod($client, 'makeRequest', ['GET', 'http://example.org', []]);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * Asserts that makeRequest will return a response when no cache implementation is provided.
     */
    public function testNullCache()
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $expectedResponse = $this->generateGuzzleResponse();
        $client = new AbstractApiClientImpl($httpClient, $this->serializer);
        $client->setResponse($expectedResponse);

        /** @var ResponseInterface $response */
        $response = $this->callMethod($client, 'makeRequest', ['GET', 'http://example.org', []]);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param int    $responseCode
     * @dataProvider dataProviderForTestIsCacheable
     */
    public function testIsCachable($method, $uri, $responseCode, $expectedResult)
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $client = new AbstractApiClientImpl($httpClient, $this->serializer);
        $response = $this->generateGuzzleResponse($responseCode);
        $isCacheable = $this->callMethod($client, 'isCacheable', [$method, $uri, [], $response]);
        $this->assertSame($expectedResult, $isCacheable);
    }

    /**
     * @return array
     */
    public function dataProviderForTestIsCacheable()
    {
        return [
            ['GET', 'videos', 200, true],
            ['GET', 'videos', 404, false],
            ['POST', 'videos', 200, false],
            ['POST', 'search', 200, true],
            ['POST', 'search', 500, false],
        ];
    }
}
