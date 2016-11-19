<?php

namespace MovingImage\Test\Client\VMPro\Factory;

use GuzzleHttp\ClientInterface;
use MovingImage\Client\VMPro\ApiClient\Guzzle5ApiClient;
use MovingImage\Client\VMPro\Factory\Guzzle5ApiClientFactory;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Client\VMPro\Subscriber\TokenSubscriber;

class Guzzle5ApiClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Guzzle5ApiClientFactory
     */
    private $factory;

    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * Set accessibility for a method on the Guzzle5ApiClientFactory
     * object to public.
     *
     * @param string $methodName
     *
     * @return \ReflectionMethod
     */
    protected function getMethod($methodName)
    {
        $class = new \ReflectionClass(Guzzle5ApiClientFactory::class);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Set up a factory + token manager instance.
     */
    public function setUp()
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '>=')) {
            $this->markTestSkipped('Skipping tests for Guzzle5ApiClientFactory when Guzzle ~6.0 is installed');
        }

        $this->factory = new Guzzle5ApiClientFactory();
        $this->tokenManager = $this->prophesize(TokenManager::class)->reveal();
    }

    /**
     * Assert whether we get the right class when we call ::getApiClientClass().
     */
    public function testGetApiClientClass()
    {
        $method = $this->getMethod('getApiClientClass');
        $this->assertEquals(Guzzle5ApiClient::class, $method->invoke($this->factory));
    }

    /**
     * Assert whether we get the right Guzzle base URI option key
     * when we call ::getGuzzleBaseUriOptionKey().
     */
    public function testGetGuzzleBaseUriOptionKey()
    {
        $method = $this->getMethod('getGuzzleBaseUriOptionKey');
        $this->assertEquals('base_url', $method->invoke($this->factory));
    }

    /**
     * Assert that a call to ::createTokenSubscriber() yields an
     * instance of the right class.
     */
    public function testCreateTokenSubscriber()
    {
        $this->assertInstanceOf(
            TokenSubscriber::class,
            $this->factory->createTokenSubscriber($this->tokenManager)
        );
    }

    /**
     * Assert whether a call to ::createHttpClient() yields an
     * instance of the right class, and whether the base URI and subscribers
     * are present in the HTTP client.
     */
    public function testCreateHttpClient()
    {
        $baseUri = 'http://test.com/';
        $subscribers = [$this->factory->createTokenSubscriber($this->tokenManager)];
        $client = $this->factory->createHttpClient($baseUri, $subscribers);

        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertEquals($baseUri, $client->getBaseUrl());
        $this->assertEquals($subscribers, $client->getDefaultOption('subscribers'));
    }
}
