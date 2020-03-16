<?php

namespace MovingImage\Test\Util;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\Channel;
use PHPUnit\Framework\TestCase;

class ChannelTraitTest extends TestCase
{
    /**
     * @var ChannelTraitImpl
     */
    private $traitObj;

    /**
     * Instantiate our ChannelTrait implementation test helper.
     */
    public function setUp(): void
    {
        $this->traitObj = new ChannelTraitImpl();
    }

    public function testSetChannelRelations()
    {
        $channels = new ArrayCollection([
            (new Channel())->setId(1)->setName('Root'),
            (new Channel())->setId(2)->setName('Level 1 child')->setParentId(1),
            (new Channel())->setId(3)->setName('Level 1 child')->setParentId(1),
            (new Channel())->setId(4)->setName('Level 2 child')->setParentId(3),
        ]);

        $channels = $this->traitObj->setChannelRelations($channels);

        $this->assertSame(1, $channels[0]->getId());
        $firstLevelChildren = $channels[0]->getChildren();
        $this->assertCount(2, $firstLevelChildren);
        $this->assertSame(2, $firstLevelChildren[0]->getId());
        $this->assertSame(3, $firstLevelChildren[1]->getId());
        $this->assertSame(1, $firstLevelChildren[0]->getParent()->getId());
        $this->assertSame(1, $firstLevelChildren[1]->getParent()->getId());
        $secondLevelChildren = $firstLevelChildren[1]->getChildren();
        $this->assertCount(1, $secondLevelChildren);
        $this->assertSame(4, $secondLevelChildren[0]->getId());
        $this->assertSame(3, $secondLevelChildren[0]->getParent()->getId());
    }
}
