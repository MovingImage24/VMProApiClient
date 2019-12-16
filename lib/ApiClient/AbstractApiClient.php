<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\ApiClient;

use MovingImage\Client\VMPro\Collection\ChannelCollection;
use MovingImage\Client\VMPro\Collection\TranscodeCollection;
use MovingImage\Client\VMPro\Collection\VideoCollection;
use MovingImage\Client\VMPro\Entity\Attachment;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\ChannelsRequestParameters;
use MovingImage\Client\VMPro\Entity\EmbedCode;
use MovingImage\Client\VMPro\Entity\Keyword;
use MovingImage\Client\VMPro\Entity\Thumbnail;
use MovingImage\Client\VMPro\Entity\UserInfo;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Entity\VideoDownloadUrl;
use MovingImage\Client\VMPro\Entity\VideoManager;
use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Client\VMPro\Util\ChannelTrait;
use MovingImage\Client\VMPro\Util\Logging\Traits\LoggerAwareTrait;
use MovingImage\Client\VMPro\Util\SearchEndpointTrait;
use MovingImage\Meta\Interfaces\ThumbnailInterface;

abstract class AbstractApiClient extends AbstractCoreApiClient implements ApiClientInterface
{
    use LoggerAwareTrait;
    use SearchEndpointTrait;
    use ChannelTrait;

    /**
     * @throws \Exception
     */
    public function getChannels(int $videoManagerId): Channel
    {
        $response = $this->makeRequest('GET', 'channels', [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        $rootChannel = $this->deserialize($response->getBody()->getContents(), Channel::class);
        $rootChannel->setChildren($this->sortChannels($rootChannel->getChildren()));

        return $rootChannel;
    }

    /**
     * Since the VMPro API doesn't sort any more the returned channels, we have to do it on our side.
     *
     * @throws \Exception
     */
    protected function sortChannels(array $channels)
    {
        array_map(function ($channel) {
            $channel->setChildren($this->sortChannels($channel->getChildren()));
        }, $channels);

        uasort($channels, function ($a, $b) {
            return $a->getName() > $b->getName();
        });

        return $channels;
    }

    public function createVideo(
        int $videoManagerId,
        string $fileName,
        ?string $title = '',
        ?string $description = '',
        ?array $channels = [],
        ?string $group = null,
        ?array $keywords = [],
        ?bool $autoPublish = null
    ): string {
        $channel = implode(',', $channels);
        $response = $this->makeRequest('POST', 'videos', [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $this->buildJsonParameters(
                compact('fileName'), // Required parameters
                compact('title', 'description', 'channel', 'group', 'keywords', 'autoPublish') // Optional parameters
            ),
        ]);

        $videoLocation = $response->getHeader('location')[0];

        $pieces = explode('/', $videoLocation);

        return end($pieces);
    }

    public function getVideos(int $videoManagerId, ?VideosRequestParameters $parameters = null): array
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        if ($parameters) {
            $query = http_build_query($parameters->getContainer(), null, '&', PHP_QUERY_RFC3986);
            $options['query'] = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $query);
        }

        $response = $this->makeRequest('GET', 'videos', $options);
        $response = json_encode(json_decode($response->getBody()->getContents(), true)['videos']);

        return $this->deserialize($response, 'array<'.Video::class.'>');
    }

    public function getCount(int $videoManagerId, ?VideosRequestParameters $parameters = null): int
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        if ($parameters) {
            $query = http_build_query($parameters->getContainer(), null, '&', PHP_QUERY_RFC3986);
            $options['query'] = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $query);
        }

        $response = $this->makeRequest('GET', 'videos', $options);

        return json_decode($response->getBody()->getContents(), true)['total'];
    }

    public function getVideoUploadUrl(int $videoManagerId, string $videoId): string
    {
        $response = $this->makeRequest('GET', sprintf('videos/%s/url', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        return $response->getHeader('location')[0];
    }

    public function updateVideo(
        int $videoManagerId,
        string $videoId,
        string $title,
        string $description,
        ?bool $autoPublish = null
    ): void {
        $this->makeRequest('PATCH', sprintf('videos/%s', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $this->buildJsonParameters([], compact('title', 'description', 'autoPublish')),
        ]);
    }

    public function addVideoToChannel(int $videoManagerId, string $videoId, string $channelId): void
    {
        $this->makeRequest('POST', sprintf('channels/%s/videos/%s', $channelId, $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);
    }

    public function removeVideoFromChannel(int $videoManagerId, string $videoId, string $channelId): void
    {
        $this->makeRequest('DELETE', sprintf('channels/%s/videos/%s', $channelId, $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);
    }

    public function setCustomMetaData(int $videoManagerId, string $videoId, array $metadata): void
    {
        $this->makeRequest('PATCH', sprintf('videos/%s/metadata', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $metadata,
        ]);
    }

    public function getEmbedCode(
        int $videoManagerId,
        string $videoId,
        string $playerDefinitionId,
        ?string $embedType = 'html'
    ): EmbedCode {
        $url = sprintf(
            'videos/%s/embed-codes?player_definition_id=%s&embed_type=%s',
            $videoId,
            $playerDefinitionId,
            $embedType
        );

        if ($this->cacheTtl) {
            $url = sprintf('%s&token_lifetime_in_seconds=%s', $url, $this->cacheTtl);
        }

        $response = $this->makeRequest('GET', $url, [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]);

        $data = \json_decode($response->getBody(), true);
        $embedCode = new EmbedCode();
        $embedCode->setCode($data['embedCode']);

        return $embedCode;
    }

    public function deleteVideo(int $videoManagerId, string $videoId): void
    {
        $this->makeRequest('DELETE', sprintf('videos/%s', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);
    }

    public function getVideo(int $videoManagerId, string $videoId, ?VideoRequestParameters $parameters = null): Video
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        if ($parameters) {
            $options['query'] = $parameters->getContainer();
        }

        $response = $this->makeRequest(
            'GET',
            sprintf('videos/%s', $videoId),
            $options
        );

        return $this->deserialize($response->getBody()->getContents(), Video::class);
    }

    public function getAttachments(int $videoManagerId, string $videoId): array
    {
        $response = $this->makeRequest(
            'GET',
            sprintf('videos/%s/attachments', $videoId),
            [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]
        );

        return $this->deserialize($response->getBody()->getContents(), 'array<'.Attachment::class.'>');
    }

    public function getChannelAttachments(int $videoManagerId, int $channelId): array
    {
        $response = $this->makeRequest(
            'GET',
            sprintf('channels/%s/attachments', $channelId),
            [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]
        );

        return $this->deserialize($response->getBody()->getContents(), 'array<'.Attachment::class.'>');
    }

    public function getKeywords(int $videoManagerId, ?string $videoId): array
    {
        $uri = is_null($videoId)
            ? 'keyword/find'
            : sprintf('videos/%s/keywords', $videoId);

        $response = $this->makeRequest(
            'GET',
            $uri,
            [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]
        );

        return $this->deserialize($response->getBody()->getContents(), 'array<'.Keyword::class.'>');
    }

    public function updateKeywords(int $videoManagerId, string $videoId, array $keywords): void
    {
        //remove all keywords
        foreach ($this->getKeywords($videoManagerId, $videoId) as $keyword) {
            $this->deleteKeyword($videoManagerId, $videoId, $keyword->getId());
        }

        //add new
        foreach ($keywords as $keyword) {
            $this->makeRequest('POST', sprintf('videos/%s/keywords', $videoId), [
                self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
                'json' => ['text' => $keyword],
            ]);
        }
    }

    public function deleteKeyword(int $videoManagerId, string $videoId, int $keywordId): void
    {
        $this->makeRequest('DELETE', sprintf('videos/%s/keywords/%s', $videoId, $keywordId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);
    }

    public function searchVideos(
        int $videoManagerId,
        ?VideosRequestParameters $parameters = null,
        ?string $searchQuery = null
    ): VideoCollection {
        $options = $this->getRequestOptionsForSearchVideosEndpoint($videoManagerId, $parameters);
        if ($searchQuery) {
            $options['query'] = sprintf('(%s) AND (%s)', $options['query'], $searchQuery);
        }
        $response = $this->makeRequest('POST', 'search', ['json' => $options]);
        $response = $this->normalizeSearchVideosResponse($response->getBody()->getContents());

        return $this->deserialize($response, VideoCollection::class);
    }

    public function searchChannels(
        int $videoManagerId,
        ?ChannelsRequestParameters $parameters = null
    ): ChannelCollection {
        $options = $this->getRequestOptionsForSearchChannelsEndpoint($videoManagerId, $parameters);
        $response = $this->makeRequest('POST', 'search', ['json' => $options]);
        $response = $this->normalizeSearchChannelsResponse($response->getBody()->getContents());
        /** @var ChannelCollection $collection */
        $collection = $this->deserialize($response, ChannelCollection::class);

        //builds parent/children relations on all channels
        $channels = $this->setChannelRelations($collection->getChannels());
        $collection->setChannels($channels);

        return $collection;
    }

    public function getVideoManagers(): array
    {
        $response = $this->makeRequest('GET', '', []);

        return $this->deserialize($response->getBody()->getContents(), 'array<'.VideoManager::class.'>');
    }

    public function getVideoDownloadUrls(int $videoManagerId, string $videoId): array
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        $response = $this->makeRequest(
            'GET',
            sprintf('videos/%s/download-urls', $videoId),
            $options
        );

        return $this->deserialize($response->getBody()->getContents(), 'array<'.VideoDownloadUrl::class.'>');
    }

    public function createThumbnailByTimestamp(int $videoManagerId, string $videoId, int $timestamp): ?ThumbnailInterface
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        $response = $this->makeRequest(
            'POST',
            'videos/'.$videoId.'/thumbnails?timestamp='.$timestamp,
            $options
        );

        if (preg_match('/\/thumbnails\/([0-9]*)/', $response->getHeader('Location')[0], $match)) {
            return (new Thumbnail())->setId($match[1]);
        }

        return null;
    }

    public function getThumbnail(int $videoManagerId, string $videoId, int $thumbnailId): ?ThumbnailInterface
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        $response = $this->makeRequest(
            'GET',
            'videos/'.$videoId.'/thumbnails/'.$thumbnailId.'/url',
            $options
        );

        $result = \json_decode($response->getBody()->getContents(), true);

        if (isset($result['downloadUrl'])) {
            return (new Thumbnail())
                ->setId($thumbnailId)
                ->setUrl($result['downloadUrl']);
        }

        return null;
    }

    public function updateThumbnail(int $videoManagerId, string $videoId, int $thumbnailId, bool $active): void
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => ['active' => $active],
        ];

        $this->makeRequest(
            'PATCH',
            'videos/'.$videoId.'/thumbnails/'.$thumbnailId,
            $options
        );
    }

    public function getUserInfo(string $token): UserInfo
    {
        $options = [
            'body' => json_encode(['jwt_id_token' => $token]),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        $response = $this->makeRequest('POST', 'corp-tube-admin/user-info', $options);

        /** @var UserInfo $userInfo */
        $userInfo = $this->deserialize($response->getBody()->getContents(), UserInfo::class);

        $userInfo->validate();

        return $userInfo;
    }

    public function getTranscodingStatus(int $videoManagerId, string $videoId): TranscodeCollection
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        $response = $this->makeRequest(
            'GET',
            'videos/'.$videoId.'/transcoding-status',
            $options
        );

        return $this->deserialize($response->getBody()->getContents(), TranscodeCollection::class);
    }
}
