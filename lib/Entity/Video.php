<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use DateTime;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use MovingImage\Meta\Interfaces\VideoInterface;

class Video implements VideoInterface
{
    /**
     * @Type("string")
     */
    private $id;

    /**
     * @Type("string")
     */
    private $title;

    /**
     * @Type("string")
     */
    private $description;

    /**
     * @Type("string")
     */
    private $thumbnail;

    /**
     * @Type("integer")
     */
    private $length;

    /**
     * @Type("integer")
     * @SerializedName("createdDate")
     */
    private $createdDate;

    /**
     * @Type("integer")
     * @SerializedName("modifiedDate")
     */
    private $modifiedDate;

    /**
     * @Type("integer")
     * @SerializedName("uploadDate")
     */
    private $uploadDate;

    /**
     * @Type("integer")
     */
    private $generation;

    /**
     * @Type("integer")
     */
    private $plays;

    /**
     * @Type("integer")
     */
    private $views;

    /**
     * @Type("boolean")
     * @SerializedName("allFormatsAvailable")
     */
    private $allFormatsAvailable;

    /**
     * @TODO replace it with array collection
     *
     * @Type("array")
     * @SerializedName("customMetadata")
     */
    private $customMetadata;

    /**
     * @TODO replace it with array collection
     *
     * @Type("array")
     */
    private $keywords;

    /**
     * @TODO replace it with array collection
     *
     * @Type("array")
     */
    private $stills;

    /**
     * @Type("boolean")
     */
    private $published;

    /**
     * @Type("array")
     */
    private $channels;

    /**
     * @Type("string")
     * @SerializedName("uploadFileName")
     */
    private $uploadFileName;

    /**
     * @Type("boolean")
     */
    private $downloadable;

    /**
     * @Type("boolean")
     * @SerializedName("audioOnly")
     */
    private $audioOnly;

    /**
     * @Type("MovingImage\Client\VMPro\Entity\CorporateTubeMetaData")
     * @SerializedName("corporateTubeMetadata")
     */
    private $corporateTubeMetadata;

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getCreatedDate(): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp(intval($this->createdDate / 1000));

        return $date;
    }

    public function setCreatedDate(DateTime $createdDate): Video
    {
        $this->createdDate = $createdDate->getTimestamp() * 1000;

        return $this;
    }

    public function getModifiedDate(): ?DateTime
    {
        $date = new DateTime();
        $date->setTimestamp(intval($this->modifiedDate / 1000));

        return $date;
    }

    public function setModifiedDate(DateTime $modifiedDate): self
    {
        $this->modifiedDate = $modifiedDate->getTimestamp() * 1000;

        return $this;
    }

    public function getUploadDate(): ?DateTime
    {
        $date = new DateTime();
        $date->setTimestamp(intval($this->uploadDate / 1000));

        return $date;
    }

    public function setUploadDate(DateTime $uploadDate): self
    {
        $this->uploadDate = $uploadDate->getTimestamp() * 1000;

        return $this;
    }

    public function getGeneration(): ?int
    {
        return $this->generation;
    }

    public function setGeneration(int $generation): self
    {
        $this->generation = $generation;

        return $this;
    }

    public function getPlays(): int
    {
        if (!$this->plays) {
            return 0;
        }

        return $this->plays;
    }

    public function setPlays(int $plays): self
    {
        $this->plays = $plays;

        return $this;
    }

    public function getViews(): int
    {
        if (!$this->views) {
            return 0;
        }

        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function areAllFormatsAvailable(): bool
    {
        if (!$this->allFormatsAvailable) {
            return false;
        }

        return $this->allFormatsAvailable;
    }

    public function setAllFormatsAvailable(bool $allFormatsAvailable): self
    {
        $this->allFormatsAvailable = $allFormatsAvailable;

        return $this;
    }

    public function getCustomMetadata(): array
    {
        if (!$this->customMetadata) {
            return [];
        }

        return $this->customMetadata;
    }

    public function setCustomMetadata(array $customMetadata): self
    {
        $this->customMetadata = $customMetadata;

        return $this;
    }

    public function getKeywords(): array
    {
        if (!$this->keywords) {
            return [];
        }

        return $this->keywords;
    }

    public function setKeywords(array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getStills(): array
    {
        if (!$this->stills) {
            return [];
        }

        //sorting preview's images from smallest to biggest
        usort($this->stills, function (array $item1, array $item2) {
            if (isset($item1['dimension']['height'], $item2['dimension']['height']) && $item1['dimension']['height'] != $item2['dimension']['height']) {
                return ($item1['dimension']['height'] > $item2['dimension']['height']) ? 1 : -1;
            }

            return 0;
        });

        return $this->stills;
    }

    public function setStills(array $stills): self
    {
        $this->stills = $stills;

        return $this;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function isPublished(): bool
    {
        if (!$this->published) {
            return false;
        }

        return $this->published;
    }

    public function setDownloadable(bool $downloadable): self
    {
        $this->downloadable = $downloadable;

        return $this;
    }

    public function isDownloadable(): bool
    {
        if (!$this->downloadable) {
            return false;
        }

        return $this->downloadable;
    }

    public function getStatus(): int
    {
        return $this->isPublished()
            ? VideoInterface::STATUS_PUBLISHED
            : VideoInterface::STATUS_NOT_PUBLISHED;
    }

    public function getChannels(): array
    {
        if (!$this->channels) {
            return [];
        }


        return $this->channels;
    }

    public function setChannels(array $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    public function getUploadFileName(): string
    {
        if (!$this->uploadFileName) {
            return '';
        }

        return $this->uploadFileName;
    }

    public function setUploadFileName(string $uploadFileName): self
    {
        $this->uploadFileName = $uploadFileName;

        return $this;
    }

    public function setCorporateTubeMetadata(CorporateTubeMetaData $corporateTubeMetadata): self
    {
        $this->corporateTubeMetadata = $corporateTubeMetadata;

        return $this;
    }

    public function getCorporateTubeMetadata(): ?CorporateTubeMetaData
    {
        return $this->corporateTubeMetadata;
    }

    public function setAudioOnly(bool $audioOnly): self
    {
        $this->audioOnly = $audioOnly;

        return $this;
    }

    public function isAudioOnly(): bool
    {
        return $this->audioOnly ?? false;
    }
}
