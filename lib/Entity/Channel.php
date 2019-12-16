<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
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
     * @Type("array<MovingImage\Client\VMPro\Entity\Channel>")
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCustomMetadata(): array
    {
        return $this->customMetadata;
    }

    public function setCustomMetadata($customMetadata): self
    {
        $this->customMetadata = $customMetadata;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getParent(): ?ChannelInterface
    {
        return $this->parent;
    }

    public function setParent(ChannelInterface $parent): self
    {
        $this->parent = $parent;
        $this->setParentId($parent->getId());

        return $this;
    }

    public function setParentOnChildren(): self
    {
        /** @var Channel $child */
        foreach ($this->getChildren() as $child) {
            $child->setParent($this);
            if (!$child->getChildren()->isEmpty()) {
                $child->setParentOnChildren();
            }
        }

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(): array
    {
        if (is_null($this->children)) {
            $this->children = [];
        }

        return $this->children;
    }

    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function addChild(ChannelInterface $child): self
    {
        $children = $this->getChildren();
        $children[] = $child;
        $this->setChildren($children);

        return $this;
    }

    public function removeChild(ChannelInterface $channel): self
    {
        $children = [];
        foreach ($this->getChildren() as $child) {
            if ($child->getId() !== $channel->getId()) {
                $children[] = $child;
            }
        }
        $this->setChildren($children);

        return $this;
    }
}
