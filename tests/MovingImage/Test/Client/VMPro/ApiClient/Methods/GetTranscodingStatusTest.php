<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Collection\TranscodeCollection;
use MovingImage\TestCase\ApiClientTestCase;

class GetTranscodingStatusTest extends ApiClientTestCase
{
    /**
     * Assert whether a specific JSON response gets properly
     * de-serialized into an object of the right type.
     */
    public function testGetTranscodingStatusBasicOk()
    {
        $httpClient = $this->createMockGuzzleClient(200, [],
            [
                [
                    'quality' => '480p',
                    'profileKey' => 'mww1560',
                    'fileExtension' => 'asf',
                    'transcodingCompleted' => true,
                ],
                [
                    'quality' => '720p',
                    'profileKey' => 'mww2420',
                    'fileExtension' => 'asf',
                    'transcodingCompleted' => false,
                ],
            ]
        );

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        /** @var TranscodeCollection $transcodeCollection */
        $transcodeCollection = $client->getTranscodingStatus(2, '74WGsUCJ3QJMjN8-LeYFpm');

        $this->assertCount(2, $transcodeCollection->getTranscodes());
        $this->assertEquals('480p', $transcodeCollection->getTranscodes()[0]->getQuality());
    }

    /**
     * Check if the response from VMPro is not an array.
     */
    public function testGetTranscodingStatusBasicError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid response from search endpoint');
        $httpClient = $this->createMockGuzzleClient(200, [], '');

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $client->getTranscodingStatus(2, '74WGsUCJ3QJMjN8-LeYFpm');
    }
}
