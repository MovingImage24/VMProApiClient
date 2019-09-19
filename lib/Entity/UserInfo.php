<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class UserInfo
{
    /**
     * @Type("string")
     */
    private $email;

    /**
     * @Type("string")
     * @SerializedName("fullName")
     */
    private $fullName;

    /**
     * @Type("array")
     * @SerializedName("videoManagerIds")
     */
    private $videoManagerIds = [];

    public function validate(): void
    {
        if (!\is_string($this->email) || !\is_string($this->fullName) || !\is_array($this->videoManagerIds)) {
            throw new \InvalidArgumentException(sprintf(
                '%s is not valid',
                self::class
            ));
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return int[]
     */
    public function getVideoManagerIds(): array
    {
        return $this->videoManagerIds;
    }
}
