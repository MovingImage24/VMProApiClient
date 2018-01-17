<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer;
use MovingImage\VMPro\TestUtil\GuzzleResponseGenerator;
use MovingImage\VMPro\TestUtil\PrivateMethodCaller;
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
    use PrivateMethodCaller;
    use GuzzleResponseGenerator;

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
        $cachedResponse = $this->generateGuzzleResponse($statusCode, $headers, $body);

        $cacheItem->method('get')->willReturn($cachedResponse);
        $cacheItem->method('isHit')->willReturn(true);
        $cachePool->method('getItem')->willReturn($cacheItem);
        $client = new AbstractApiClientImpl($httpClient, $serializer, $cachePool);

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
        $serializer = $this->createMock(Serializer::class);
        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $expectedResponse = $this->generateGuzzleResponse();
        $cacheItem->method('isHit')->willReturn(false);
        $cachePool->method('getItem')->willReturn($cacheItem);
        $client = new AbstractApiClientImpl($httpClient, $serializer, $cachePool);
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
        $serializer = $this->createMock(Serializer::class);
        $expectedResponse = $this->generateGuzzleResponse();
        $client = new AbstractApiClientImpl($httpClient, $serializer);
        $client->setResponse($expectedResponse);

        /** @var ResponseInterface $response */
        $response = $this->callMethod($client, 'makeRequest', ['GET', 'http://example.org', []]);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param int    $responseCode
     * @covers \AbstractApiClient::isCacheable()
     * @dataProvider dataProviderForTestIsCacheable
     */
    public function testIsCachable($method, $uri, $responseCode, $expectedResult)
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(Serializer::class);
        $client = new AbstractApiClientImpl($httpClient, $serializer);
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
