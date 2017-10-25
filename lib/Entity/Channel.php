<?php

namespace MovingImage\Client\VMPro\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

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
     * @Type("integer")
     * @SerializedName("parentId")
     */
    private $parentId = null;

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

    /**
     * @param Channel $parent
     *
     * @return Channel
     */
    public function setParent(Channel $parent)
    {
        $this->parent = $parent;
        $this->setParentId($parent->getId());

        return $this;
    }

    public function setParentOnChildren()
    {
        /** @var Channel $child */
        foreach ($this->getChildren() as $child) {
            $child->setParent($this);
            if (!$child->getChildren()->isEmpty()) {
                $child->setParentOnChildren();
            }
        }
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     *
     * @return Channel
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return ArrayCollection<Channel>
     */
    public function getChildren()
    {
        if (is_null($this->children)) {
            $this->children = new ArrayCollection();
        }

        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     *
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @param Channel $child
     *
     * @return Channel
     */
    public function addChild(Channel $child)
    {
        $this->getChildren()->add($child);

        return $this;
    }
}
