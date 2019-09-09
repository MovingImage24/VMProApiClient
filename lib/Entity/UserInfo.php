<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

class UserInfo
{
    /**
     * @Type("string")
     */
    private $email;

    /**
     * @Type("string")
     */
    private $fullName;

    /**
     * @Type("array")
     */
    private $videoManagerIds = [];
}