<?php

namespace MovingImage\Client\VMPro\Collection;

use MovingImage\Client\VMPro\Entity\Channel;
use JMS\Serializer\Annotation as JMS;

class ChannelCollection
{
    /**
     * @var int
     * @JMS\Type("integer")
     * @JMS\SerializedName("totalCount")
     */
    private $totalCount;

    /**
     * @var Channel[]
     * @JMS\Type("array<MovingImage\Client\VMPro\Entity\Channel>")
     */
    private $channels;

    /**
     * @param int       $totalCount
     * @param Channel[] $channels
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
     * @return Channel[]
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
     * @param Channel[] $channels
     *
     * @return ChannelCollection
     */
    public function setChannels(array $channels)
    {
        $this->channels = $channels;

        return $this;
    }
}
