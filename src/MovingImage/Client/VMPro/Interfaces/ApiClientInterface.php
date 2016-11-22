<?php

namespace MovingImage\Client\VMPro\Interfaces;

use MovingImage\Client\VMPro\Entity\Channel;

/**
 * Interface ApiClientInterface.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
interface ApiClientInterface
{
    /**
     * @const Version indicator to determine compatibility.
     */
    const VERSION = '0.1';

    /**
     * Get all channels for a specific video manager.
     *
     * @param int $videoManagerId
     *
     * @return Channel
     */
    public function getChannels($videoManagerId);

    /**
     * Create a new Video entity in the video manager.
     *
     * @param int    $videoManagerId
     * @param string $fileName
     * @param string $title
     * @param string $description
     * @param null   $channel
     * @param null   $group
     * @param array  $keywords
     * @param bool   $autoPublish
     *
     * @return string The video ID of the newly created video
     */
    public function createVideo(
        $videoManagerId,
        $fileName,
        $title = '',
        $description = '',
        $channel = null,
        $group = null,
        array $keywords = [],
        $autoPublish = true
    );

    /**
     * Get the upload URL for a specific video.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     *
     * @return string The video's upload URL
     */
    public function getVideoUploadUrl($videoManagerId, $videoId);
}
