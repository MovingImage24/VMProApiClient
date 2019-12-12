<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Exception;
use MovingImage\TestCase\ApiClientTestCase;

class CreateVideoTest extends ApiClientTestCase
{
    /**
     * Assert whether the video ID is sliced from the response header's URL
     * appropriately.
     */
    public function testCorrectResponse()
    {
        $httpClient = $this->createMockGuzzleClient(200, [
            'Location' => 'http://videomanagerpro.com/video/kljadsfe390_ioASDJr',
        ]);

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $res = $client->createVideo(5, 'example.mp4');

        $this->assertEquals('kljadsfe390_ioASDJr', $res);
    }

    /**
     * Assert whether all of the required parameters are being transmitted
     * into the HTTP request object.
     */
    public function testRequiredParametersTransmitted()
    {
        $httpClient = $this->createMockGuzzleClient(200, [
            'Location' => 'http://videomanagerpro.com/video/kljadsfe390_ioASDJr',
        ]);

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $res = $client->createVideo(5, 'example.mp4');
        $params = json_decode($this->getLastRequest()->getBody(), true);

        $this->assertArrayHasKey('fileName', $params);
        $this->assertEquals('example.mp4', $params['fileName']);
    }

    /**
     * Assert whether the appropriate exception is thrown when the required
     * parameter 'fileName' is missing.
     *
     * @expectedException \Exception
     */
    public function testMissingRequiredParameters()
    {
        $httpClient = $this->createMockGuzzleClient(200, [
            'Location' => 'http://videomanagerpro.com/video/kljadsfe390_ioASDJr',
        ]);

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $client->createVideo(5, '');
    }

    /**
     * Assert whether all of the optional parameters are being transmitted
     * into the HTTP request object, and whether the values are passed correctly.
     */
    public function testOptionalParametersTransmitted()
    {
        $httpClient = $this->createMockGuzzleClient(200, [
            'Location' => 'http://videomanagerpro.com/video/kljadsfe390_ioASDJr',
        ]);

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $res = $client->createVideo(5, 'example.mp4', 'Example', 'Description', 6, 5, ['test'], false);
        $params = json_decode($this->getLastRequest()->getBody(), true);

        $this->assertArrayHasKey('title', $params);
        $this->assertArrayHasKey('description', $params);
        $this->assertArrayHasKey('channel', $params);
        $this->assertArrayHasKey('group', $params);
        $this->assertArrayHasKey('keywords', $params);
        $this->assertArrayHasKey('autoPublish', $params);

        $this->assertEquals('Example', $params['title']);
        $this->assertEquals('Description', $params['description']);
        $this->assertEquals(6, $params['channel']);
        $this->assertEquals(5, $params['group']);
        $this->assertEquals(['test'], $params['keywords']);
        $this->assertFalse($params['autoPublish']);
    }

    /**
     * Assert whether the right exception is thrown when a non-existing channel ID
     * is provided to the method.
     *
     * @expectedException \Exception
     */
    public function testCreateButChannelNotExist()
    {
        $httpClient = $this->createMockGuzzleClient(404, [], [
            'message' => 'entity not found',
        ]);

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $client->createVideo(5, 'example.mp4', '', '', 34890534905);
    }
}
