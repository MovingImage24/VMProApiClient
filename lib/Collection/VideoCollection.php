<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Collection;

use JMS\Serializer\Annotation as JMS;
use MovingImage\Meta\Interfaces\VideoInterface;

class VideoCollection
{
    /**
     * @var int
     * @JMS\Type("integer")
     * @JMS\SerializedName("totalCount")
     */
    private $totalCount;

    /**
     * @var VideoInterface[]
     * @JMS\Type("array<MovingImage\Client\VMPro\Entity\Video>")
     */
    private $videos;

    /**
     * @param VideoInterface[] $videos
     */
    public function __construct(int $totalCount, array $videos)
    {
        $this->totalCount = $totalCount;
        $this->videos = $videos;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return VideoInterface[]
     */
    public function getVideos(): array
    {
        return $this->videos;
    }

    public function setTotalCount(int $totalCount): VideoCollection
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @param VideoInterface[] $videos
     */
    public function setVideos(array $videos): VideoCollection
    {
        $this->videos = $videos;

        return $this;
    }
}
