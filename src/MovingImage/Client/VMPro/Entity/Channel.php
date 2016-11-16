<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;

/**
 * Class Channel.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class Channel
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
     * @Type("ArrayCollection<MovingImage\Client\VMPro\Entity\Channel>")
     */
    private $children;

    /**
     * @Type("MovingImage\Client\VMPro\Entity\Channel")
     */
    private $parent = null;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Channel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Channel
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Channel
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setParentOnChildren()
    {
        foreach ($this->getChildren() as $child) {
            $child->parent = $this;
        }
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }
}
