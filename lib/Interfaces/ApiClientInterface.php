<?php

namespace MovingImage\Client\VMPro\Interfaces;

use MovingImage\Client\VMPro\Collection\ChannelCollection;
use MovingImage\Client\VMPro\Collection\VideoCollection;
use MovingImage\Client\VMPro\Entity\Attachment;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\ChannelsRequestParameters;
use MovingImage\Client\VMPro\Entity\EmbedCode;
use MovingImage\Client\VMPro\Entity\Keyword;
use MovingImage\Client\VMPro\Entity\VideoManager;
use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Meta\Interfaces\VideoInterface;

/**
 * Interface ApiClientInterface.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 * @author Omid Rad <omid.rad@movingimage.com>
 */
interface ApiClientInterface
{
    /**
     * @const Version indicator to determine compatibility.
     */
    const VERSION = '0.2';

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
     * Get list of videos.
     *
     * @param int                     $videoManagerId
     * @param VideosRequestParameters $parameters
     *
     * @return VideoInterface[] Collection of videos
     */
    public function getVideos($videoManagerId, VideosRequestParameters $parameters = null);

    /**
     * Get the upload URL for a specific video.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     *
     * @return string The video's upload URL
     */
    public function getVideoUploadUrl($videoManagerId, $videoId);

    /**
     * Update a video with new values.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     * @param string $title
     * @param string $description
     */
    public function updateVideo($videoManagerId, $videoId, $title, $description);

    /**
     * Add a video to one or more channels.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     * @param string $channelId
     */
    public function addVideoToChannel($videoManagerId, $videoId, $channelId);

    /**
     * Add/remove/update custom metadata fields to a video.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     * @param array  $metadata
     */
    public function setCustomMetaData($videoManagerId, $videoId, $metadata);

    /**
     * Retrieve an embed code for a specific player definition + video ID.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     * @param string $playerDefinitionId
     * @param string $embedType
     *
     * @return EmbedCode
     */
    public function getEmbedCode($videoManagerId, $videoId, $playerDefinitionId, $embedType = 'html');

    /**
     * Delete a video.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     */
    public function deleteVideo($videoManagerId, $videoId);

    /**
     * @param int                    $videoManagerId
     * @param string                 $videoId
     * @param VideoRequestParameters $parameters
     *
     * @return Video
     */
    public function getVideo($videoManagerId, $videoId, VideoRequestParameters $parameters = null);

    /**
     * @param                              $videoManagerId
     * @param VideosRequestParameters|null $parameters
     *
     * @return mixed
     */
    public function getCount($videoManagerId, VideosRequestParameters $parameters = null);

    /**
     * @param int    $videoManagerId
     * @param string $videoId
     *
     * @return Attachment[]
     */
    public function getAttachments($videoManagerId, $videoId);

    /**
     * @param int    $videoManagerId
     * @param string $videoId
     *
     * @return Keyword[]
     */
    public function getKeywords($videoManagerId, $videoId);

    /**
     * @param int    $videoManagerId
     * @param string $videoId
     * @param array  $keywords
     */
    public function updateKeywords($videoManagerId, $videoId, $keywords);

    /**
     * @param int    $videoManagerId
     * @param string $videoId
     * @param int    $keywordId
     */
    public function deleteKeyword($videoManagerId, $videoId, $keywordId);

    /**
     * Get list of videos using the search endpoint.
     * This method is a temporary solution and therefore you should not rely on it.
     * It will be removed in the future.
     *
     * @deprecated
     *
     * @param int                     $videoManagerId
     * @param VideosRequestParameters $parameters
     *
     * @return VideoCollection
     */
    public function searchVideos($videoManagerId, VideosRequestParameters $parameters = null);

    /**
     * Get channels using the search endpoint.
     * This method is a temporary solution and therefore you should not rely on it.
     * It will be removed in the future.
     *
     * @deprecated
     *
     * @param int                       $videoManagerId
     * @param ChannelsRequestParameters $parameters
     *
     * @return ChannelCollection
     */
    public function searchChannels($videoManagerId, ChannelsRequestParameters $parameters = null);

    /**
     * Get video managers.
     *
     * @return VideoManager[]
     */
    public function getVideoManagers();
}
