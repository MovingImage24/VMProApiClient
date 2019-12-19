<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Collection\ChannelCollection;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class SearchChannelsTest extends ApiClientTestCase
{
    /**
     * @covers \AbstractApiClient::searchChannels()
     */
    public function testSearchChannels()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('searchChannels'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        /** @var ChannelCollection $collection */
        $collection = $client->searchChannels(1);
        $this->assertSame(62, $collection->getTotalCount());
        $this->assertSame(33981, $collection->getChannels()[0]->getId());

        foreach ($collection->getChannels() as $channel) {
            $this->assertRelations($channel);
        }
    }

    /**
     * Asserts parent/children relations for the provided channel.
     */
    private function assertRelations(Channel $channel)
    {
        /** @var Channel $child */
        foreach ($channel->getChildren() as $child) {
            $this->assertSame($channel->getId(), $child->getParentId());
            $this->assertRelations($child);
            if ($channel->getParent()) {
                $this->assertSame($channel->getParentId(), $channel->getParent()->getId());
            }
        }
    }
}
