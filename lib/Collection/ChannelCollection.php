<?php

namespace MovingImage\Client\VMPro\Collection;

use JMS\Serializer\Annotation as JMS;
use MovingImage\Client\VMPro\Interfaces\ChannelInterface;

class ChannelCollection
{
    /**
     * @var int
     * @JMS\Type("integer")
     * @JMS\SerializedName("totalCount")
     */
    private $totalCount;

    /**
     * @var ChannelInterface[]
     * @JMS\Type("array<MovingImage\Client\VMPro\Entity\Channel>")
     */
    private $channels;

    /**
     * @param int                $totalCount
     * @param ChannelInterface[] $channels
     */
    public function __construct($totalCount, array $channels)
    {
        $this->totalCount = $totalCount;
        $this->channels = $channels;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return ChannelInterface[]
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param int $totalCount
     *
     * @return ChannelCollection
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @param ChannelInterface[] $channels
     *
     * @return ChannelCollection
     */
    public function setChannels(array $channels)
    {
        $this->channels = $channels;

        return $this;
    }
}
