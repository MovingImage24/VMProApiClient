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
     *
     * @var DateTime
     */
    private $uploadDate;

    /**
     * @Type("string")
     * @SerializedName("uploaderUserId")
     *
     * @var string
     */
    private $uploaderUserId;

    /**
     * @Type("string")
     * @SerializedName("inChargeUserId")
     *
     * @var string
     */
    private $inChargeUserId;

    public function getUploadDate(): DateTime
    {
        return $this->uploadDate;
    }

    public function setUploadDate(DateTime $uploadDate): void
    {
        $this->uploadDate = $uploadDate;
    }

    public function getUploaderUserId(): string
    {
        return $this->uploaderUserId;
    }

    public function setUploaderUserId(string $uploaderUserId): void
    {
        $this->uploaderUserId = $uploaderUserId;
    }

    public function getInChargeUserId(): string
    {
        return $this->inChargeUserId;
    }

    public function setInChargeUserId(string $inChargeUserId): void
    {
        $this->inChargeUserId = $inChargeUserId;
    }

}
