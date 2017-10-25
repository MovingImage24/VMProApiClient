<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Collection\VideoCollection;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class SearchVideosTest extends ApiClientTestCase
{
    /**
     * @covers \AbstractApiClient::searchVideos()
     */
    public function testSearchVideos()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('searchVideos'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        /** @var VideoCollection $collection */
        $collection = $client->searchVideos(1);

        $this->assertSame(10, $collection->getTotalCount());
        $this->assertSame('1wGJbtN7QPwkAkd8_VcgKH', $collection->getVideos()[0]->getId());
    }
}
