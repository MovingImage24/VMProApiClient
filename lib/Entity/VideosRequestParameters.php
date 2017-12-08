<?php

namespace MovingImage\Client\VMPro\Entity;

use MovingImage\Client\VMPro\Util\AccessorTrait;
use MovingImage\Meta\Enums\PublicationState;

/**
 * Class VideosRequestParameters.
 *
 * @method int getVideoId()
 * @method VideosRequestParameters setVideoId(int $videoId)
 * @method int getChannelId()
 * @method VideosRequestParameters setChannelId(int $channelId)
 * @method int getOffset()
 * @method VideosRequestParameters setOffset(int $offset)
 * @method int getLimit()
 * @method VideosRequestParameters setLimit(int $limit)
 * @method string getOrder()
 * @method string getOrderProperty()
 * @method VideosRequestParameters setOrderProperty(string $orderProperty)
 * @method string getSearchTerm()
 * @method VideosRequestParameters setSearchTerm(string $searchTerm)
 * @method bool isIncludeCustomMetadata()
 * @method VideosRequestParameters setIncludeCustomMetadata(bool $includeCustomMetadata)
 * @method string getCustomMetadataField()
 * @method VideosRequestParameters setCustomMetadataField(int $customMetadataField)
 * @method string getSearchInField()
 * @method VideosRequestParameters setSearchInField(string $searchInField)
 * @method string getPublicationState()
 * @method bool isIncludeKeywords()
 * @method VideosRequestParameters setIncludeKeywords(bool $includeKeywords)
 * @method VideosRequestParameters setIncludeChannelAssignments(bool $includeChannels)
 * @method bool isIncludeSubChannels()
 * @method VideosRequestParameters setIncludeSubChannels(bool $includeSubChannels)
 * @method VideosRequestParameters setMetadataSetKey(string $metadataSetKey)
 * @method string getMetadataSetKey()
 *
 * @author Omid Rad <omid.rad@movingimage.com>
 */
class VideosRequestParameters
{
    use AccessorTrait;

    /**
     * @param string $order
     *
     * @return VideosRequestParameters
     */
    public function setOrder($order)
    {
        $pool = ['asc', 'desc'];

        // Silently ignore wrong values
        if (in_array($order, $pool)) {
            $this->container['order'] = $order;
        }

        return $this;
    }

    /**
     * @param string $publicationState
     *
     * @return VideosRequestParameters
     */
    public function setPublicationState($publicationState)
    {
        if (in_array($publicationState, PublicationState::getValues())) {
            $this->container['publication_state'] = $publicationState;
        }

        return $this;
    }
}
