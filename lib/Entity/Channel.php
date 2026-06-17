<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use MovingImage\Meta\Interfaces\ChannelInterface;

class Channel implements ChannelInterface
{
    /**
     * @var int
     */
    #[JMS\Type('integer')]
    private $id;

    /**
     * @var string
     */
    #[JMS\Type('string')]
    private $name;

    /**
     * @var string
     */
    #[JMS\Type('string')]
    private $description;

    /**
     * @var array
     */
    #[JMS\Type('array')]
    #[JMS\SerializedName('customMetadata')]
    private $customMetadata = [];

    /**
     * @var ArrayCollection<ChannelInterface>
     */
    #[JMS\Type('ArrayCollection<MovingImage\Client\VMPro\Entity\Channel>')]
    private $children;

    /**
     * @var ChannelInterface
     */
    #[JMS\Type('MovingImage\Client\VMPro\Entity\Channel')]
    private $parent = null;

    #[JMS\Type('integer')]
    #[JMS\SerializedName('parentId')]
    private $parentId = null;

    /**
     * @var Ownership
     */
    #[JMS\Type('MovingImage\Client\VMPro\Entity\Ownership')]
    private $ownership = null;

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

    public function setCustomMetadata(array $customMetadata): self
    {
        $this->customMetadata = $customMetadata;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
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
    public function getChildren(): ArrayCollection
    {
        if (is_null($this->children)) {
            $this->children = new ArrayCollection();
        }

        return $this->children;
    }

    public function setChildren(ArrayCollection $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function addChild(ChannelInterface $child): self
    {
        $this->getChildren()->add($child);

        return $this;
    }

    public function removeChild(ChannelInterface $channel): self
    {
        $this->getChildren()->removeElement($channel);

        return $this;
    }

    public function getOwnership(): Ownership
    {
        return $this->ownership;
    }

    public function setOwnership(Ownership $ownership): self
    {
        $this->ownership = $ownership;

        return $this;
    }
}
