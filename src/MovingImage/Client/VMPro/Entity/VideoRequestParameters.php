<?php

namespace MovingImage\Client\VMPro\Entity;

use MovingImage\VMProApiClient\Util\AccessorTrait;

/**
 * Class VideosRequestParameters.
 *
 * @method boolean isIncludeCustomMetadata()
 * @method VideoRequestParameters setIncludeCustomMetadata(boolean $includeCustomMetadata)
 *
 * @method string getCustomMetadataField()
 * @method VideoRequestParameters setCustomMetadataField(string $customMetadataField)
 *
 * @method boolean isIncludeKeywords()
 * @method VideoRequestParameters setIncludeKeywords(boolean $includeKeywords)
 *
 * @method int getRelatedVideosChannelId()
 * @method VideoRequestParameters setRelatedVideosChannelId(int $relatedVideosChannelId)
 *
 * @method boolean isIgnorePublicationState()
 * @method VideoRequestParameters setIgnorePublicationState(boolean $ignorePublicationState)
 *
 * @author Robert Szeker <robert.szeker@movingimage.com>
 */
class VideoRequestParameters
{
    use AccessorTrait;
}
