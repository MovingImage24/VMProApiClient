<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Collection;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var ArrayCollection<VideoInterface>
     * @JMS\Type("ArrayCollection<MovingImage\Client\VMPro\Entity\Video>")
     */
    private $videos;

    /**
     * @param ArrayCollection<VideoInterface> $videos
     */
    public function __construct(int $totalCount, ArrayCollection $videos)
    {
        $this->totalCount = $totalCount;
        $this->videos = $videos;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return ArrayCollection<VideoInterface>
     */
    public function getVideos(): ArrayCollection
    {
        return $this->videos;
    }

    public function setTotalCount(int $totalCount): VideoCollection
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @param ArrayCollection<VideoInterface> $videos
     */
    public function setVideos(ArrayCollection $videos): VideoCollection
    {
        $this->videos = $videos;

        return $this;
    }
}
