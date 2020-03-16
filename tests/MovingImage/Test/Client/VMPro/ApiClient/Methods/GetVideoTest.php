<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetVideoTest extends ApiClientTestCase
{
    /**
     * Assert whether a specific JSON response gets properly
     * de-serialized into an object of the right type.
     */
    public function testGetVideoBasic()
    {
        $id = '74WGsUCJ3QJMjN8-LeYFpm';
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getVideo'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $res = $client->getVideo(1, $id);

        $this->assertInstanceOf(Video::class, $res);
        $this->assertEquals($id, $res->getId());
        $this->assertEquals(34, $res->getCorporateTubeMetadata()->getInChargeUserId());
    }
}
