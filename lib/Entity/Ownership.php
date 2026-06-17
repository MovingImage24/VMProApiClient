<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation as JMS;

class Ownership
{
    /**
     * @var int
     */
    #[JMS\Type('integer')]
    #[JMS\SerializedName('ownerGroupId')]
    private $ownerGroupId;

    /**
     * @var bool
     */
    #[JMS\Type('boolean')]
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
