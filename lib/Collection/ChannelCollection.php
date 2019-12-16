<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Collection;

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
     * @var ChannelInterface[]
     * @JMS\Type("array<MovingImage\Client\VMPro\Entity\Channel>")
     */
    private $channels;

    /**
     * @param ChannelInterface[] $channels
     */
    public function __construct(int $totalCount, array $channels)
    {
        $this->totalCount = $totalCount;
        $this->channels = $channels;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return ChannelInterface[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    public function setTotalCount(int $totalCount): ChannelCollection
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @param ChannelInterface[] $channels
     */
    public function setChannels(array $channels): ChannelCollection
    {
        $this->channels = $channels;

        return $this;
    }
}
