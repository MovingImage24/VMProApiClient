<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use MovingImage\Client\VMPro\Util\AccessorTrait;

/**
 * @method int                       getOffset()
 * @method ChannelsRequestParameters setOffset(int $offset)
 * @method int                       getLimit()
 * @method ChannelsRequestParameters setLimit(int $limit)
 * @method string                    getOrder()
 * @method string                    getOrderProperty()
 * @method ChannelsRequestParameters setOrderProperty(string $orderProperty)
 * @method string                    getSearchTerm()
 * @method ChannelsRequestParameters setSearchTerm(string $searchTerm)
 * @method string                    getSearchInField()
 * @method ChannelsRequestParameters setSearchInField(string $searchInField)
 * @method ChannelsRequestParameters setMetadataSetKey(string $metadataSetKey)
 * @method string                    getMetadataSetKey()
 */
class ChannelsRequestParameters
{
    use AccessorTrait;

    public function setOrder(string $order): self
    {
        $pool = ['asc', 'desc'];

        // Silently ignore wrong values
        if (in_array($order, $pool)) {
            $this->container['order'] = $order;
        }

        return $this;
    }
}
