<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use DateTime;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class CorporateTubeMetaData
{
    /**
     * @Type("DateTime")
     * @SerializedName("uploadDate")
     *
     * @var DateTime|null
     */
    private $uploadDate;

    /**
     * @Type("string")
     * @SerializedName("uploaderUserId")
     *
     * @var string|null
     */
    private $uploaderUserId;

    /**
     * @Type("string")
     * @SerializedName("uploaderKeycloakUserId")
     *
     * @var string|null
     */
    private $uploaderKeycloakUserId;

    /**
     * @Type("string")
     * @SerializedName("inChargeUserId")
     *
     * @var string|null
     */
    private $inChargeUserId;

    /**
     * @Type("string")
     * @SerializedName("inChargeKeycloakUserId")
     *
     * @var string|null
     */
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
