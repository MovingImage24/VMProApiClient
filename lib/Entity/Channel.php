<?php

namespace MovingImage\Client\VMPro\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use MovingImage\Meta\Interfaces\ChannelInterface;

class Channel implements ChannelInterface
{
    /**
     * @Type("integer")
     *
     * @var int
     */
    private $id;

    /**
     * @Type("string")
     *
     * @var string
     */
    private $name;

    /**
     * @Type("string")
     *
     * @var string
     */
    private $description;

    /**
     * @Type("array")
     * @SerializedName("customMetadata")
     *
     * @var array
     */
    private $customMetadata = [];

    /**
     * @Type("ArrayCollection<MovingImage\Client\VMPro\Entity\Channel>")
     *
     * @var ChannelInterface[]
     */
    private $children;

    /**
     * @Type("MovingImage\Client\VMPro\Entity\Channel")
     *
     * @var ChannelInterface
     */
    private $parent = null;

    /**
     * @Type("integer")
     * @SerializedName("parentId")
     */
    private $parentId = null;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomMetadata()
    {
        return $this->customMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomMetadata($customMetadata)
    {
        $this->customMetadata = $customMetadata;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(ChannelInterface $parent)
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function addChild(ChannelInterface $child)
    {
        $this->getChildren()->add($child);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(ChannelInterface $channel)
    {
        $this->getChildren()->removeElement($channel);

        return $this;
    }
}
