<?php

namespace MovingImage\Test\Client\VMPro\Factory;

use GuzzleHttp\HandlerStack;
use MovingImage\Client\VMPro\ApiClient;
use MovingImage\Client\VMPro\Factory\Guzzle6ApiClientFactory;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Client\VMPro\Middleware\TokenMiddleware;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class Guzzle6ApiClientFactoryTest extends TestCase
{
    use ProphecyTrait;
    /**
     * @var Guzzle6ApiClientFactory
     */
    private $factory;

    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * Set accessibility for a method
     * object to public.
     *
     * @param string $methodName
     *
     * @return \ReflectionMethod
     */
    protected function getMethod($methodName)
    {
        $class = new \ReflectionClass(Guzzle6ApiClientFactory::class);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Set up a factory + token manager instance.
     */
    public function setUp(): void
    {
        $this->factory = new Guzzle6ApiClientFactory();
        $this->tokenManager = $this->prophesize(TokenManager::class)->reveal();
    }

    /**
     * Assert whether we get the right class when we call ::getApiClientClass().
     */
    public function testGetApiClientClass()
    {
        $method = $this->getMethod('getApiClientClass');
        self::assertEquals(ApiClient::class, $method->invoke($this->factory));
    }

    /**
     * Assert whether we get the right Guzzle base URI option key
     * when we call ::getGuzzleBaseUriOptionKey().
     */
    public function testGetGuzzleBaseUriOptionKey()
    {
        $method = $this->getMethod('getGuzzleBaseUriOptionKey');
        self::assertEquals('base_uri', $method->invoke($this->factory));
    }

    /**
     * Assert whether a call to ::createTokenMiddleware() yields
     * an instance of the right class.
     */
    public function testCreateTokenMiddleware()
    {
        self::assertInstanceOf(
            TokenMiddleware::class,
            $this->factory->createTokenMiddleware($this->tokenManager)
        );
    }

    /**
     * Assert whether a call to ::createHttpClient() yeilds
     * an instance of the right class, whether the 'base_uri' configuration
     * parameter is populated with the right value, and whether our token
     * middleware is present in the Guzzle Client stack handler.
     */
    public function testCreateHttpClient()
    {
        $baseUri = 'http://test.com/';
        $middlewares = [$this->factory->createTokenMiddleware($this->tokenManager)];
        $client = $this->factory->createHttpClient($baseUri, $middlewares);

        self::assertEquals($baseUri, $client->getConfig('base_uri'));

        $reflectionClass = new \ReflectionClass(HandlerStack::class);
        $stackProperty = $reflectionClass->getProperty('stack');
        $stackProperty->setAccessible(true);

        $stackHandler = $client->getConfig('handler');
        $stack = $stackProperty->getValue($stackHandler);

        $inStack = false;
        foreach ($stack as $stackItem) {
            if ($stackItem[0] instanceof TokenMiddleware) {
                $inStack = true;
            }
        }

        self::assertTrue($inStack, 'TokenMiddleware was not found in Guzzle Client StackHandler');
    }
}
