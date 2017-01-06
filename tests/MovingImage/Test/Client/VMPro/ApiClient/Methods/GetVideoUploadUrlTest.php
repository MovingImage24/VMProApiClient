<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Exception;
use MovingImage\TestCase\ApiClientTestCase;

/**
 * Class GetUploadVideoUrlTest.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class GetVideoUploadUrlTest extends ApiClientTestCase
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
        $res = $client->getVideoUploadUrl(5, 'dfkjLADSI34d_iwe');

        $this->assertEquals('http://videomanagerpro.com/video/kljadsfe390_ioASDJr', $res);
    }

    /**
     * Assert whether the right exception is thrown when calling ::getVideoUploadUrl
     * with a video ID that does not exist in the video manager.
     *
     * @expectedException \Exception
     */
    public function testWithNonExistantVideoId()
    {
        $httpClient = $this->createMockGuzzleClient(400, [], [
            'message' => 'an error occurred',
        ]);

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $client->getVideoUploadUrl(5, 'sdlkfjdslkfjdklsfjkld');
    }
}
