<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use MovingImage\Meta\Interfaces\AttachmentInterface;

class Attachment implements AttachmentInterface
{
    /**
     * @Type("string")
     */
    private $id;

    /**
     * @Type("string")
     * @SerializedName("fileName")
     */
    private $fileName;

    /**
     * @Type("string")
     * @SerializedName("downloadUrl")
     */
    private $downloadUrl;

    /**
     * @Type("int")
     * @SerializedName("fileSize")
     */
    private $fileSize;

    /**
     * @Type("string")
     */
    private $type;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }

    public function setDownloadUrl(string $downloadUrl): self
    {
        $this->downloadUrl = $downloadUrl;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
