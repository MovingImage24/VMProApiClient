<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use MovingImage\Meta\Interfaces\VideoDownloadUrlInterface;

class VideoDownloadUrl implements VideoDownloadUrlInterface
{
    /**
     * @Type("string")
     * @SerializedName("quality")
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
     * @SerializedName("url")
     */
    private $url;

    /**
     * @Type("integer")
     * @SerializedName("fileSize")
     */
    private $fileSize;

    public function getQuality(): string
    {
        return $this->quality;
    }

    public function setQuality(string $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function getProfileKey(): string
    {
        return $this->profileKey;
    }

    public function setProfileKey(string $profileKey): self
    {
        $this->profileKey = $profileKey;

        return $this;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function setFileExtension(string $fileExtension): self
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }
}
