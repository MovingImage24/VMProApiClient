<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Util;

use MovingImage\Client\VMPro\Entity\Channel;

/**
 * Helper methods for manipulating Channels.
 */
trait ChannelTrait
{
    /**
     * Configures the parent/child relationships between Channels.
     *
     * @param Channel[] $channels - any iterable collection of Channel
     */
    private function setChannelRelations(array $channels): array
    {
        $indexedChannels = [];

        /** @var Channel $channel */
        foreach ($channels as $channel) {
            $indexedChannels[$channel->getId()] = $channel;
        }

        foreach ($indexedChannels as $channel) {
            $parentId = $channel->getParentId();
            if ($parentId && array_key_exists($parentId, $indexedChannels)) {
                /** @var Channel $parent */
                $parent = $indexedChannels[$parentId];
                $channel->setParent($parent);
                $parent->addChild($channel);
            } else {
                //search endpoint returns a parent ID even for the root channel (!?!)
                //but it doesn't include that parent channel in the response
                //this is why we set the parentId to null when we detect that the channel is missing from response
                $channel->setParentId(null);
            }
        }

        return array_values($indexedChannels);
    }
}
