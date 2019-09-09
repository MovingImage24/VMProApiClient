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
}