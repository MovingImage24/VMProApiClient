<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

/**
 * Class VideoThumbnail
 */
class VideoThumbnail
{
    /**
     * @Type("string")
     */
    private $quality;

    /**
     * @Type("string")
     */
    private $url;

    /**
     * @Type("array<string, int>")
     */
    private $dimension;

    /**
     * @return mixed
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param string $quality
     *
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * @param mixed $dimension
     *
     * @return $this
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;

        return $this;
    }
}