<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use DateTime;
use JMS\Serializer\Annotation as JMS;

class CorporateTubeMetaData
{
    /**
     * @var DateTime|null
     */
    #[JMS\Type('DateTime')]
    #[JMS\SerializedName('uploadDate')]
    private $uploadDate;

    /**
     * @var string|null
     */
    #[JMS\Type('string')]
    #[JMS\SerializedName('uploaderUserId')]
    private $uploaderUserId;

    /**
     * @var string|null
     */
    #[JMS\Type('string')]
    #[JMS\SerializedName('uploaderKeycloakUserId')]
    private $uploaderKeycloakUserId;

    /**
     * @var string|null
     */
    #[JMS\Type('string')]
    #[JMS\SerializedName('inChargeUserId')]
    private $inChargeUserId;

    /**
     * @var string|null
     */
    #[JMS\Type('string')]
    #[JMS\SerializedName('inChargeKeycloakUserId')]
    private $inChargeKeycloakUserId;

    public function getUploadDate(): ?DateTime
    {
        return $this->uploadDate;
    }

    public function setUploadDate(DateTime $uploadDate): self
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    public function getUploaderUserId(): ?string
    {
        return $this->uploaderUserId;
    }

    public function setUploaderUserId(?string $uploaderUserId): self
    {
        $this->uploaderUserId = $uploaderUserId;

        return $this;
    }

    public function getInChargeUserId(): ?string
    {
        return $this->inChargeUserId;
    }

    public function setInChargeUserId(?string $inChargeUserId): self
    {
        $this->inChargeUserId = $inChargeUserId;

        return $this;
    }

    public function getUploaderKeycloakUserId(): ?string
    {
        return $this->uploaderKeycloakUserId;
    }

    public function setUploaderKeycloakUserId(?string $uploaderKeycloakUserId): self
    {
        $this->uploaderKeycloakUserId = $uploaderKeycloakUserId;

        return $this;
    }

    public function getInChargeKeycloakUserId(): ?string
    {
        return $this->inChargeKeycloakUserId;
    }

    public function setInChargeKeycloakUserId(?string $inChargeKeycloakUserId): self
    {
        $this->inChargeKeycloakUserId = $inChargeKeycloakUserId;

        return $this;
    }
}
