<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

/**
 * Class VideoTranscodingStatus
 */
class VideoTranscodingStatus
{
    /**
     * @Type("string")
     */
    private $quality;

    /**
     * @Type("string")
     * @Serializer\SerializedName("profileKey")
     */
    private $profileKey;

    /**
     * @Type("string")
     * @Serializer\SerializedName("fileExtension")
     */
    private $fileExtension;

    /**
     * @Type("bool")
     * @Serializer\SerializedName("transcodingCompleted")
     */
    private $transcodingCompleted;

    /**
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param string $quality
     *
     * @return VideoTranscodingStatus
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * @return string
     */
    public function getProfileKey()
    {
        return $this->profileKey;
    }

    /**
     * @param string $profileKey
     *
     * @return VideoTranscodingStatus
     */
    public function setProfileKey($profileKey)
    {
        $this->profileKey = $profileKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @param string $fileExtension
     *
     * @return VideoTranscodingStatus
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranscodingCompleted()
    {
        return $this->transcodingCompleted;
    }

    /**
     * @param mixed $transcodingCompleted
     *
     * @return VideoTranscodingStatus
     */
    public function setTranscodingCompleted($transcodingCompleted)
    {
        $this->transcodingCompleted = $transcodingCompleted;

        return $this;
    }
}