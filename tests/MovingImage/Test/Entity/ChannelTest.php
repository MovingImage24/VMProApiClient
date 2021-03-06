<?php

namespace MovingImage\Test\Entity;

use MovingImage\Client\VMPro\Entity\Channel;
use PHPUnit\Framework\TestCase;

class ChannelTest extends TestCase
{
    public function testSetParent()
    {
        $channel = new Channel();
        $channel->setParent((new Channel())->setId(42));
        $this->assertSame(42, $channel->getParentId());
    }

    public function testAddRemoveChild()
    {
        $channel = new Channel();
        $children = [];
        for ($i = 1; $i <= 10; ++$i) {
            $children[$i] = (new Channel())->setId($i);
            $channel->addChild($children[$i]);
        }

        $this->assertCount(10, $channel->getChildren());
        $this->assertChildrenInChannel($channel, range(1, 10));

        //remove all even IDs
        for ($i = 2; $i <= 10; $i += 2) {
            $channel->removeChild($children[$i]);
        }

        $this->assertCount(5, $channel->getChildren());
        $this->assertChildrenInChannel($channel, [1, 3, 5, 7, 9]);
    }

    /**
     * Asserts that the provided channel contains all the children specified in $expectedIds argument.
     *
     * @param Channel $channel
     * @param array   $expectedIds
     */
    private function assertChildrenInChannel(Channel $channel, array $expectedIds)
    {
        $presentIds = [];
        foreach ($channel->getChildren() as $child) {
            $presentIds[] = $child->getId();
        }

        $this->assertSame($expectedIds, $presentIds);
    }
}
