<?php

namespace MovingImage\Client\VMPro\Interfaces;

use MovingImage\Client\VMPro\Entity\Channel;

interface ApiClientInterface
{
    /**
     * Get all channels for a specific video manager.
     *
     * @param int $videoManagerId
     *
     * @return Channel
     */
    public function getChannels($videoManagerId);
}
