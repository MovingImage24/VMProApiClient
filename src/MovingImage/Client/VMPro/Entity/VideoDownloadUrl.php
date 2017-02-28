<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;

/**
 * Class VideoDownloadUrl.
 */
class VideoDownloadUrl
{
    /**
     * @Type("string")
     */
    private $quality;

    /**
     * @Type("string")
     */
    private $profileKey;

    /**
     * @Type("string")
     */
    private $fileExtension;

    /**
     * @Type("string")
     */
    private $url;

    /**
     * @Type("integer")
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
     * @return VideoDownloadUrl
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
     * @return VideoDownloadUrl
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
     * @return VideoDownloadUrl
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
     * @return VideoDownloadUrl
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
     * @return VideoDownloadUrl
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }
}
