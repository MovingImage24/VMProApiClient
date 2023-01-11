<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class MetaDataSet
{
    /**
     * @Type("integer")
     * @SerializedName("id")
     */
    private int $id;

    /**
     * @Type("string")
     * @SerializedName("keyName")
     */
    private string $keyName;

    /**
     * @Type("string")
     * @SerializedName("type")
     */
    private string $type;

    /**
     * @Type("boolean")
     * @SerializedName("isDefault")
     */
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
