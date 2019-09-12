<?php

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

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return int[]
     */
    public function getVideoManagerIds()
    {
        return $this->videoManagerIds;
    }


}
