<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\Client;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\Exception;

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
}
