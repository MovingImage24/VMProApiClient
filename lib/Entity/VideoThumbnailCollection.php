<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

/**
 * Class VideoThumbnailCollection
 */
class VideoThumbnailCollection
{
    /**
     * @Type("int")
     */
    private $id;

    /**
     * @Type("bool")
     */
    private $active;

    /**
     * @Type("array<MovingImage\Client\VMPro\Entity\VideoThumbnail>")
     */
    private $items;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return VideoThumbnailCollection
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     *
     * @return VideoThumbnailCollection
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     *
     * @return VideoThumbnailCollection
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }
}