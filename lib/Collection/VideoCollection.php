<?php

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
     * @param int              $totalCount
     * @param VideoInterface[] $videos
     */
    public function __construct($totalCount, array $videos)
    {
        $this->totalCount = $totalCount;
        $this->videos = $videos;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return VideoInterface[]
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * @param int $totalCount
     *
     * @return VideoCollection
     */
    public function setTotalCount(int $totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @param VideoInterface[] $videos
     *
     * @return VideoCollection
     */
    public function setVideos(array $videos)
    {
        $this->videos = $videos;

        return $this;
    }
}
