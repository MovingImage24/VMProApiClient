<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Collection\ChannelCollection;
use MovingImage\Client\VMPro\Collection\VideoCollection;
use MovingImage\Client\VMPro\Entity\Attachment;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\ChannelsRequestParameters;
use MovingImage\Client\VMPro\Entity\CorporateTubeMetaData;
use MovingImage\Client\VMPro\Entity\EmbedCode;
use MovingImage\Client\VMPro\Entity\Keyword;
use MovingImage\Client\VMPro\Entity\Player;
use MovingImage\Client\VMPro\Entity\Transcode;
use MovingImage\Client\VMPro\Entity\UserInfo;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Entity\VideoDownloadUrl;
use MovingImage\Client\VMPro\Entity\VideoManager;
use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Exception\NotFoundException;
use MovingImage\Meta\Interfaces\ThumbnailInterface;
use MovingImage\Meta\Interfaces\VideoInterface;

interface ApiClientInterface
{
    /**
     * @const Version indicator to determine compatibility.
     */
    public const VERSION = '0.2';

    /**
     * Get all channels for a specific video manager.
     */
    public function getChannels(int $videoManagerId, ?string $locale = null): Channel;

    /**
     * Get a specific channel.
     *
     * @throws NotFoundException
     */
    public function getChannel(int $videoManagerId, int $channelId, ?string $locale = null): Channel;

    /**
     * Create a new Video entity in the video manager.
     *
     * @return string The video ID of the newly created video
     */
    public function createVideo(
        int $videoManagerId,
        string $fileName,
        ?string $title = '',
        ?string $description = '',
        ?int $channel = null,
        ?string $group = null,
        ?array $keywords = [],
        ?bool $autoPublish = true
    ): string;

    /**
     * Get list of videos.
     *
     * @return ArrayCollection<VideoInterface> Collection of videos
     */
    public function getVideos(int $videoManagerId, ?VideosRequestParameters $parameters = null): ArrayCollection;

    /**
     * Get the upload URL for a specific video.
     *
     * @return string The video's upload URL
     */
    public function getVideoUploadUrl(int $videoManagerId, string $videoId): string;

    /**
     * Update a video with new values.
     */
    public function updateVideo(
        int $videoManagerId,
        string $videoId,
        string $title,
        string $description,
        ?bool $autoPublish = null
    ): void;

    /**
     * Add a video to a channel.
     */
    public function addVideoToChannel(int $videoManagerId, string $videoId, int $channelId): void;

    /**
     * Remove a video from a channel.
     */
    public function removeVideoFromChannel(int $videoManagerId, string $videoId, int $channelId): void;

    /**
     * Add/remove/update custom metadata fields to a video.
     */
    public function setCustomMetaData(int $videoManagerId, string $videoId, array $metadata): void;

    /**
     * Retrieve an embed code for a specific player definition + video ID.
     */
    public function getEmbedCode(
        int $videoManagerId,
        string $videoId,
        string $playerDefinitionId,
        string $embedType = 'html'
    ): EmbedCode;

    /**
     * Delete a video.
     */
    public function deleteVideo(int $videoManagerId, string $videoId): void;

    public function getVideo(int $videoManagerId, string $videoId, ?VideoRequestParameters $parameters = null): Video;

    public function getCount(int $videoManagerId, ?VideosRequestParameters $parameters = null): int;

    /**
     * @return ArrayCollection<Attachment>
     */
    public function getAttachments(int $videoManagerId, string $videoId): ArrayCollection;

    /**
     * Returns attachments for the specified channel.
     *
     * @return ArrayCollection<Attachment>
     */
    public function getChannelAttachments(int $videoManagerId, int $channelId): ArrayCollection;

    /**
     * Get keywords, either for specific video if videoId is given, or for all videos of given videoManager.
     *
     * @return ArrayCollection<Keyword>
     */
    public function getKeywords(int $videoManagerId, ?string $videoId): ArrayCollection;

    /**
     * Update video keywords.
     */
    public function updateKeywords(int $videoManagerId, string $videoId, array $keywords): void;

    /**
     * Delete keyword from video by keyword id.
     */
    public function deleteKeyword(int $videoManagerId, string $videoId, int $keywordId): void;

    /**
     * Get list of videos using the search endpoint.
     * This method is a temporary solution and therefore you should not rely on it.
     * It will be removed in the future.
     *
     * @deprecated
     */
    public function searchVideos(
        int $videoManagerId,
        ?VideosRequestParameters $parameters = null,
        ?string $searchQuery = null
    ): VideoCollection;

    /**
     * Get channels using the search endpoint.
     * This method is a temporary solution and therefore you should not rely on it.
     * It will be removed in the future.
     *
     * @deprecated
     */
    public function searchChannels(
        int $videoManagerId,
        ?ChannelsRequestParameters $parameters = null
    ): ChannelCollection;

    /**
     * Get video managers.
     *
     * @return ArrayCollection<VideoManager>
     */
    public function getVideoManagers(): ArrayCollection;

    /**
     * Get download-URLs including file size of the specified video.
     *
     * @return ArrayCollection<VideoDownloadUrl> The video download URLs
     */
    public function getVideoDownloadUrls(int $videoManagerId, string $videoId): ArrayCollection;

    public function createThumbnailByTimestamp(
        int $videoManagerId,
        string $videoId,
        int $timestamp
    ): ?ThumbnailInterface;

    public function getThumbnail(int $videoManagerId, string $videoId, int $thumbnailId): ?ThumbnailInterface;

    public function updateThumbnail(int $videoManagerId, string $videoId, int $thumbnailId, bool $active): void;

    public function getUserInfo(string $token): UserInfo;

    /**
     * @return ArrayCollection<Transcode>
     */
    public function getTranscodingStatus(int $videoManagerId, string $videoId): ArrayCollection;

    /** @return ArrayCollection<Player> */
    public function getPlayers(int $videoManagerId): ArrayCollection;

    public function getCorporateTubeMetadata(int $videoManagerId, string $videoId): CorporateTubeMetaData;

    public function updateCorporateTubeMetadata(
        int $videoManagerId,
        string $videoId,
        CorporateTubeMetaData $corporateTubeMetaData
    ): void;

    public function getMetaDataSets(int $videoManagerId): ArrayCollection;
}
