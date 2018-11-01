<?php

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\HandlerCallback;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\DeserializationContext;
use MovingImage\Meta\Interfaces\AttachmentInterface;

/**
 * Class Attachment.
 */
class Attachment implements AttachmentInterface
{
    /**
     * @Type("string")
     */
    private $id;

    /**
     * @Type("string")
     */
    private $fileName;

    /**
     * @Type("string")
     */
    private $downloadUrl;

    /**
     * @Type("int")
     */
    private $fileSize;

    /**
     * @Type("string")
     */
    private $type;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return Attachment
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param $fileName
     *
     * @return Attachment
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * @param $downloadUrl
     *
     * @return Attachment
     */
    public function setDownloadUrl($downloadUrl)
    {
        $this->downloadUrl = $downloadUrl;

        return $this;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param $fileSize
     *
     * @return Attachment
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     *
     * @return Attachment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @HandlerCallback("json",  direction = "deserialization")
     */
    public function deserializeFromJson(JsonDeserializationVisitor $visitor, array $data, DeserializationContext $context)
    {
        if (isset($data['data']['id'])) {
            $this->id = $data['data']['id'];
        }

        if (isset($data['data']['fileName'])) {
            $this->fileName = $data['data']['fileName'];
        }

        if (isset($data['data']['downloadUrl'])) {
            $this->downloadUrl = $data['data']['downloadUrl'];
        }

        if (isset($data['data']['fileSize'])) {
            $this->fileSize = $data['data']['fileSize'];
        }

        if (isset($data['type']['name'])) {
            $this->type = $data['type']['name'];
        }
    }
}
