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
     * @SerializedName("inChargeUserId")
     *
     * @var string|null
     */
    private $inChargeUserId;

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

    public function setUploaderUserId(string $uploaderUserId): self
    {
        $this->uploaderUserId = $uploaderUserId;

        return $this;
    }

    public function getInChargeUserId(): ?string
    {
        return $this->inChargeUserId;
    }

    public function setInChargeUserId(string $inChargeUserId): self
    {
        $this->inChargeUserId = $inChargeUserId;

        return $this;
    }
}
