<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;
use MovingImage\Meta\Interfaces\VideoManagerInterface;

/**
 * Class VideoManager.
 */
class VideoManager implements VideoManagerInterface
{
    /**
     * @Type("integer")
     */
    private $id;

    /**
     * @Type("string")
     */
    private $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id integer
     *
     * @return VideoManager
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name string
     *
     * @return VideoManager
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
