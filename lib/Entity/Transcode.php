<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation as JMS;
use MovingImage\Meta\Interfaces\TranscodeInterface;

class Transcode implements TranscodeInterface
{
    #[JMS\Type('string')]
    private $quality;

    #[JMS\Type('string')]
    #[JMS\SerializedName('profileKey')]
    private $profileKey;

    #[JMS\Type('string')]
    #[JMS\SerializedName('fileExtension')]
    private $fileExtension;

    #[JMS\Type('boolean')]
    #[JMS\SerializedName('transcodingCompleted')]
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
