<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\TestCase\ApiClientTestCase;

class GetChannelsTest extends ApiClientTestCase
{
    /**
     * Assert whether a specific JSON response gets properly
     * de-serialized into an object of the right type.
     */
    public function testGetChannelsBasic(): void
    {
        $httpClient = $this->createMockGuzzleClient(200, [], [
            'id' => 5,
            'name' => 'root_channel',
        ]);

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $res = $client->getChannels(5);

        $this->assertInstanceOf(Channel::class, $res);
        $this->assertEquals(5, $res->getId());
        $this->assertEquals('root_channel', $res->getName());
    }
}
