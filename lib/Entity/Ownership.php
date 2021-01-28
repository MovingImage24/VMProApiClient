<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class Ownership
{
    /**
     * @Type("integer")
     * @SerializedName("ownerGroupId")
     *
     * @var int
     */
    private $ownerGroupId;

    /**
     * @Type("boolean")
     *
     * @var bool
     */
    private $visibility;

    public function getOwnerGroupId(): int
    {
        return $this->ownerGroupId;
    }

    public function setOwnerGroupId(int $ownerGroupId): Ownership
    {
        $this->ownerGroupId = $ownerGroupId;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): Ownership
    {
        $this->visibility = $visibility;

        return $this;
    }


}
