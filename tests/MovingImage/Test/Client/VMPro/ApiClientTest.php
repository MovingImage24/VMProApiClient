<?php

namespace MovingImage\Test\Client\VMPro;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\ApiClient;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Set up instances of Guzzle client + JMS Serializer.
     */
    public function setUp(): void
    {
        $this->httpClient = new Client();
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * Assert whether the ApiClient stub class in the namespace root
     * namespace inherits upon the appropriate client subclass.
     */
    public function testAppropriateInheritance()
    {
        $apiClient = new ApiClient($this->httpClient, $this->serializer);
        $this->assertInstanceOf(ApiClient\Guzzle6ApiClient::class, $apiClient);
    }
}
