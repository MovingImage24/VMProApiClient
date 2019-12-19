<?php

namespace MovingImage\Client\VMPro\Interfaces;

use MovingImage\Client\VMPro\Collection\ChannelCollection;
use MovingImage\Client\VMPro\Collection\VideoCollection;
use MovingImage\Client\VMPro\Entity\Attachment;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\ChannelsRequestParameters;
use MovingImage\Client\VMPro\Entity\EmbedCode;
use MovingImage\Client\VMPro\Entity\Keyword;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Entity\VideoDownloadUrl;
use MovingImage\Client\VMPro\Entity\VideoManager;
use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Meta\Interfaces\ThumbnailInterface;
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
     * @param int       $videoManagerId
     * @param string    $videoId
     * @param string    $title
     * @param string    $description
     * @param bool|null $autoPublish
     */
    public function updateVideo($videoManagerId, $videoId, $title, $description, $autoPublish = null);

    /**
     * Add a video to a channel.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     * @param string $channelId
     */
    public function addVideoToChannel($videoManagerId, $videoId, $channelId);

    /**
     * Remove a video from a channel.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     * @param string $channelId
     */
    public function removeVideoFromChannel($videoManagerId, $videoId, $channelId);

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
     * @param $videoManagerId
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
     * Returns attachments for the specified channel.
     *
     * @param int $videoManagerId
     * @param int $channelId
     *
     * @return Attachment[]
     */
    public function getChannelAttachments($videoManagerId, $channelId);

    /**
     * Get keywords, either for specific video if videoId is given, or for all videos of given videoManager.
     *
     * @param int         $videoManagerId
     * @param string|null $videoId
     *
     * @return Keyword[]
     */
    public function getKeywords($videoManagerId, $videoId);

    /**
     * Update video keywords.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     * @param array  $keywords
     */
    public function updateKeywords($videoManagerId, $videoId, $keywords);

    /**
     * Delete keyword from video by keyword id.
     *
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
     * @param string                  $searchQuery
     *
     * @return VideoCollection
     */
    public function searchVideos($videoManagerId, VideosRequestParameters $parameters = null, $searchQuery = null);

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

    /**
     * Get download-URLs including file size of the specified video.
     *
     * @param int    $videoManagerId
     * @param string $videoId
     *
     * @return VideoDownloadUrl[] The video download URLs
     */
    public function getVideoDownloadUrls($videoManagerId, $videoId);

    /**
     * @param $videoManagerId
     * @param $videoId
     * @param $timestamp
     *
     * @return ThumbnailInterface
     */
    public function createThumbnailByTimestamp($videoManagerId, $videoId, $timestamp);

    /**
     * @param $videoManagerId
     * @param $videoId
     * @param $thumbnailId
     *
     * @return ThumbnailInterface|null
     */
    public function getThumbnail($videoManagerId, $videoId, $thumbnailId);

    /**
     * @param $videoManagerId
     * @param $videoId
     * @param $thumbnailId
     * @param $active
     */
    public function updateThumbnail($videoManagerId, $videoId, $thumbnailId, $active);
}
