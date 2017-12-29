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
     * @Type("string")
     */
    private $id;

    /**
     * @Type("string")
     */
    private $name;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
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
     * @param $name
     *
     * @return VideoManager
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
