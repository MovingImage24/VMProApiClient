<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class VideoDownloadInfo
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
     * @Type("string")
     */
    private $url;

    /**
     * @Type("integer")
     * @SerializedName("fileSize")
     */
    private $fileSize;

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
     * @return VideoDownloadInfo
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
     * @return VideoDownloadInfo
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
     * @return VideoDownloadInfo
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return VideoDownloadInfo
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     *
     * @return VideoDownloadInfo
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }
}
