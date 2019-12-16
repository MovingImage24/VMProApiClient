<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use MovingImage\Client\VMPro\Util\AccessorTrait;

/**
 * @method bool                   isIncludeCustomMetadata()
 * @method VideoRequestParameters setIncludeCustomMetadata(bool $includeCustomMetadata)
 * @method string                 getCustomMetadataField()
 * @method VideoRequestParameters setCustomMetadataField(string $customMetadataField)
 * @method bool                   isIncludeKeywords()
 * @method VideoRequestParameters setIncludeKeywords(bool $includeKeywords)
 * @method int                    getRelatedVideosChannelId()
 * @method VideoRequestParameters setRelatedVideosChannelId(int $relatedVideosChannelId)
 * @method bool                   isIgnorePublicationState()
 * @method VideoRequestParameters setIgnorePublicationState(bool $ignorePublicationState)
 */
class VideoRequestParameters
{
    use AccessorTrait;
}
