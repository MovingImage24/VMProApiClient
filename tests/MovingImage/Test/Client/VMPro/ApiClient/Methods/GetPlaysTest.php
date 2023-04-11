<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetPlaysTest extends ApiClientTestCase
{
    public function testGetPlays()
    {
        $id = '74WGsUCJ3QJMjN8-LeYFpm';
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getPlays'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $plays = $client->getPlays(1, $id);

        self::assertSame(1, $plays);
    }

    public function testGetPlaysVideoDoesNotExist()
    {
        $id = '74WGsUCJ3QJMjN8-LeYFpm';
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getPlaysEmpty'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $plays = $client->getPlays(1, $id);

        self::assertSame(0, $plays);
    }
}
