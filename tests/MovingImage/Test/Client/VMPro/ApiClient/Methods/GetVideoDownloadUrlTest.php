<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Entity\VideoDownloadInfo;
use MovingImage\TestCase\ApiClientTestCase;

class GetVideoDownloadUrlTest extends ApiClientTestCase
{
    /**
     * Assert whether a specific JSON response gets properly
     * de-serialized into an object of the right type.
     */
    public function testGetVideoDownloadUrl()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], [
           [
              'quality' => '108p',
              'profileKey' => 'xmv224',
              'fileExtension' => 'webm',
              'url' => 'http://domain.tv/89uvAHPPPCfHAGWAgmrJL4.xmv224.webm',
              'fileSize' => 11234450,
           ],
        ]);

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $res = $client->getVideoDownloadUrl(5, 'sfasdfasdfasdasd');

        $this->assertInternalType('array', $res);
        $this->assertEquals(1, count($res));
        $this->assertInstanceOf(VideoDownloadInfo::class, $res[0]);
        $this->assertEquals(
            (new VideoDownloadInfo())
                ->setQuality('108p')
                ->setProfileKey('xmv224')
                ->setFileExtension('webm')
                ->setUrl('http://domain.tv/89uvAHPPPCfHAGWAgmrJL4.xmv224.webm')
                ->setFileSize(11234450),
            $res[0]
        );
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
        $client->getVideoDownloadUrl(5, 'sdlkfjdslkfjdklsfjkld');
    }
}
