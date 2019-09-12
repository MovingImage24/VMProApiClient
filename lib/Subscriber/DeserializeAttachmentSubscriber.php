<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Subscriber;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use MovingImage\Client\VMPro\Entity\Attachment;

class DeserializeAttachmentSubscriber implements SubscribingHandlerInterface
{

    /**
     * Return format:
     *
     *      array(
     *          array(
     *              'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
     *              'format' => 'json',
     *              'type' => 'DateTime',
     *              'method' => 'serializeDateTimeToJson',
     *          ),
     *      )
     *
     * The direction and method keys can be omitted.
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => Attachment::class,
                'method' => 'deserialize',
            ]
        ];
    }
    public function deserialize(JsonDeserializationVisitor $visitor, array $data, array $type, DeserializationContext $context)
    {
        $attachment = new Attachment();
        if (isset($data['data']['id'])) {
            $attachment->setId($data['data']['id']);
        }

        if (isset($data['data']['fileName'])) {
            $attachment->setFileName($data['data']['fileName']);
        }

        if (isset($data['data']['downloadUrl'])) {
            $attachment->setDownloadUrl($data['data']['downloadUrl']);
        }

        if (isset($data['data']['fileSize'])) {
            $attachment->setFileSize($data['data']['fileSize']);
        }

        if (isset($data['type']['name'])) {
            $attachment->setType($data['type']['name']);
        }

        return $attachment;
    }

}