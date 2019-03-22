<?php

namespace MovingImage\Client\VMPro\Entity;

use MovingImage\Meta\Interfaces\ThumbnailInterface;

class Thumbnail implements ThumbnailInterface
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $url;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Thumbnail
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Thumbnail
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
