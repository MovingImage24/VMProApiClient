<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation as JMS;

class MetaDataSet
{
    #[JMS\Type('integer')]
    #[JMS\SerializedName('id')]
    private int $id;

    #[JMS\Type('string')]
    #[JMS\SerializedName('keyName')]
    private string $keyName;

    #[JMS\Type('string')]
    #[JMS\SerializedName('type')]
    private string $type;

    #[JMS\Type('boolean')]
    #[JMS\SerializedName('isDefault')]
    private bool $isDefault;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getKeyName(): string
    {
        return $this->keyName;
    }

    public function setKeyName(string $keyName): self
    {
        $this->keyName = $keyName;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault ?? false;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }
}
