<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use MovingImage\Meta\Interfaces\ChannelInterface;

class ChannelCollection
{
    /**
     * @var int
     * @JMS\Type("integer")
     * @JMS\SerializedName("totalCount")
     */
    private $totalCount;

    /**
     * @var ArrayCollection<ChannelInterface>
     * @JMS\Type("ArrayCollection<MovingImage\Client\VMPro\Entity\Channel>")
     */
    private $channels;

    /**
     * @param ArrayCollection<ChannelInterface> $channels
     */
    public function __construct(int $totalCount, ArrayCollection $channels)
    {
        $this->totalCount = $totalCount;
        $this->channels = $channels;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return ArrayCollection<ChannelInterface>
     */
    public function getChannels(): ArrayCollection
    {
        return $this->channels;
    }

    public function setTotalCount(int $totalCount): ChannelCollection
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @param ArrayCollection<ChannelInterface> $channels
     */
    public function setChannels(ArrayCollection $channels): ChannelCollection
    {
        $this->channels = $channels;

        return $this;
    }
}
