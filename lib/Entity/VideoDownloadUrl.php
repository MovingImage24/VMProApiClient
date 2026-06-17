<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation as JMS;
use MovingImage\Meta\Interfaces\VideoDownloadUrlInterface;

class VideoDownloadUrl implements VideoDownloadUrlInterface
{
    #[JMS\Type('string')]
    #[JMS\SerializedName('quality')]
    private $quality;

    #[JMS\Type('string')]
    #[JMS\SerializedName('profileKey')]
    private $profileKey;

    #[JMS\Type('string')]
    #[JMS\SerializedName('fileExtension')]
    private $fileExtension;

    #[JMS\Type('string')]
    #[JMS\SerializedName('url')]
    private $url;

    #[JMS\Type('integer')]
    #[JMS\SerializedName('fileSize')]
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
