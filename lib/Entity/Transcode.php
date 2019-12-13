<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use MovingImage\Meta\Interfaces\TranscodeInterface;

class Transcode implements TranscodeInterface
{
    /**
     * @Type("string")
     */
    private $quality;

    /**
     * @Type("string")
     * @SerializedName("profileKey")
     */
    private $profileKey;

    /**
     * @Type("string")
     * @SerializedName("fileExtension")
     */
    private $fileExtension;

    /**
     * @Type("boolean")
     * @SerializedName("transcodingCompleted")
     */
    private $completed;

    public function getQuality(): string
    {
        return $this->quality;
    }

    public function getProfileKey(): string
    {
        return $this->profileKey;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }
}
