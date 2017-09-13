<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use MovingImage\Meta\Interfaces\VideoInterface;

/**
 * Class Video.
 *
 * @author Omid Rad <omid.rad@movingimage.com>
 */
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
     * @param string $id
     *
     * @return Video
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Video
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Video
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     *
     * @return Video
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     *
     * @return Video
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        $date = new \DateTime();
        $date->setTimestamp(substr($this->createdDate, 0, 10));

        return $date;
    }

    /**
     * @param int $createdDate
     *
     * @return Video
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    /**
     * @param int $modifiedDate
     *
     * @return Video
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * @param int $uploadDate
     *
     * @return Video
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getGeneration()
    {
        return $this->generation;
    }

    /**
     * @param int $generation
     *
     * @return Video
     */
    public function setGeneration($generation)
    {
        $this->generation = $generation;

        return $this;
    }

    /**
     * @return int
     */
    public function getPlays()
    {
        return $this->plays;
    }

    /**
     * @param int $plays
     *
     * @return Video
     */
    public function setPlays($plays)
    {
        $this->plays = $plays;

        return $this;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param int $views
     *
     * @return Video
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAllFormatsAvailable()
    {
        return $this->allFormatsAvailable;
    }

    /**
     * @param bool $allFormatsAvailable
     *
     * @return Video
     */
    public function setAllFormatsAvailable($allFormatsAvailable)
    {
        $this->allFormatsAvailable = $allFormatsAvailable;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomMetadata()
    {
        return $this->customMetadata;
    }

    /**
     * @param array $customMetadata
     *
     * @return Video
     */
    public function setCustomMetadata($customMetadata)
    {
        $this->customMetadata = $customMetadata;

        return $this;
    }

    /**
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     *
     * @return Video
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @return array
     */
    public function getStills()
    {
        //sorting preview's images from smallest to biggest
        usort($this->stills, function (array $item1, array $item2) {
            if (isset($item1['dimension']['height'], $item2['dimension']['height'])) {
                return $item1['dimension']['height'] > $item2['dimension']['height'];
            }
            return -1;
        });

        return $this->stills;
    }

    /**
     * @param array $stills
     *
     * @return Video
     */
    public function setStills($stills)
    {
        $this->stills = $stills;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     *
     * @return Video
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return $this->getPublished();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->isPublished()
            ? VideoInterface::STATUS_PUBLISHED
            : VideoInterface::STATUS_NOT_PUBLISHED;
    }

    /**
     * @return mixed
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param mixed $channels
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
    }
}
