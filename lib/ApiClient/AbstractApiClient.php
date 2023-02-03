<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\ApiClient;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Collection\ChannelCollection;
use MovingImage\Client\VMPro\Collection\VideoCollection;
use MovingImage\Client\VMPro\Entity\Attachment;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\ChannelsRequestParameters;
use MovingImage\Client\VMPro\Entity\CorporateTubeMetaData;
use MovingImage\Client\VMPro\Entity\EmbedCode;
use MovingImage\Client\VMPro\Entity\Keyword;
use MovingImage\Client\VMPro\Entity\MetaDataSet;
use MovingImage\Client\VMPro\Entity\Player;
use MovingImage\Client\VMPro\Entity\Thumbnail;
use MovingImage\Client\VMPro\Entity\Transcode;
use MovingImage\Client\VMPro\Entity\UserInfo;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Entity\VideoDownloadUrl;
use MovingImage\Client\VMPro\Entity\VideoManager;
use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Exception\NotFoundException;
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
    public function getChannels(int $videoManagerId, string $locale): Channel
    {
        $options = [self::OPT_VIDEO_MANAGER_ID => $videoManagerId];
        $options['query'][self::LOCALE] = $locale;
        $response = $this->makeRequest('GET', 'channels', $options);

        /** @var Channel $rootChannel */
        $rootChannel = $this->deserialize($response->getBody()->getContents(), Channel::class);
        $rootChannel->setChildren($this->sortChannels($rootChannel->getChildren()));

        return $rootChannel;
    }

    public function getChannel(int $videoManagerId, int $channelId, string $locale): Channel
    {
        $channel = $this->findChannel($this->getChannels($videoManagerId, $locale), $channelId);

        if (!$channel instanceof Channel) {
            throw new NotFoundException('channel not found');
        }

        return $channel;
    }

    private function findChannel(Channel $rootChannel, int $channelId): ?Channel
    {
        if ($rootChannel->getId() === $channelId) {
            return $rootChannel;
        }

        foreach ($rootChannel->getChildren() as $child) {
            $foundChannel = $this->findChannel($child, $channelId);
            if ($foundChannel instanceof Channel) {
                return $foundChannel;
            }
        }

        return null;
    }

    /**
     * Since the VMPro API doesn't sort any more the returned channels, we have to do it on our side.
     *
     * @throws \Exception
     */
    protected function sortChannels(ArrayCollection $channels)
    {
        $channels->map(function ($channel) {
            $channel->setChildren($this->sortChannels($channel->getChildren()));
        });

        $iterator = $channels->getIterator();
        $iterator->uasort(function ($a, $b) {
            /* @var Channel $a */
            /* @var Channel $b */
            return $a->getName() > $b->getName();
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function createVideo(
        int $videoManagerId,
        string $fileName,
        ?string $title = '',
        ?string $description = '',
        ?int $channel = null,
        ?string $group = null,
        ?array $keywords = [],
        ?bool $autoPublish = null
    ): string {
        $response = $this->makeRequest('POST', 'videos', [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $this->buildJsonParameters(
                compact('fileName'), // Required parameters
                compact(
                    'title',
                    'description',
                    'channel',
                    'group',
                    'keywords',
                    'autoPublish'
                ) // Optional parameters
            ),
        ]);

        $videoLocation = $response->getHeader('location')[0];

        $pieces = explode('/', $videoLocation);

        return end($pieces);
    }

    public function getVideos(int $videoManagerId, ?VideosRequestParameters $parameters = null): ArrayCollection
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        if ($parameters) {
            $query = http_build_query($parameters->getContainer(), '', '&', PHP_QUERY_RFC3986);
            $options['query'] = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $query);
            $options['query'] = str_replace('channel_id%5B%5D', 'channel_id', $options['query']);
        }

        $response = $this->makeRequest('GET', 'videos', $options);
        $response = json_encode(json_decode($response->getBody()->getContents(), true)['videos']);

        return $this->deserialize($response, 'ArrayCollection<'.Video::class.'>');
    }

    public function getCount(int $videoManagerId, ?VideosRequestParameters $parameters = null): int
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        if ($parameters) {
            $query = http_build_query($parameters->getContainer(), '', '&', PHP_QUERY_RFC3986);
            $options['query'] = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $query);
            $options['query'] = str_replace('channel_id%5B%5D', 'channel_id', $options['query']);
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

    public function addVideoToChannel(int $videoManagerId, string $videoId, int $channelId): void
    {
        $this->makeRequest('POST', sprintf('channels/%u/videos/%s', $channelId, $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);
    }

    public function removeVideoFromChannel(int $videoManagerId, string $videoId, int $channelId): void
    {
        $this->makeRequest('DELETE', sprintf('channels/%u/videos/%s', $channelId, $videoId), [
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
        string $embedType = 'html'
    ): EmbedCode {
        $url = sprintf(
            'videos/%s/embed-codes?player_definition_id=%s&embed_type=%s',
            $videoId,
            $playerDefinitionId,
            $embedType
        );

        $response = $this->makeRequest('GET', $url, [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]);

        $data = \json_decode($response->getBody()->getContents(), true);
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

    /**
     * {@inheritdoc}
     */
    public function getAttachments(int $videoManagerId, string $videoId): ArrayCollection
    {
        $response = $this->makeRequest(
            'GET',
            sprintf('videos/%s/attachments', $videoId),
            [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]
        );

        $response = $this->normalizeGetAttachmentsResponse($response->getBody()->getContents());

        return $this->deserialize($response, 'ArrayCollection<'.Attachment::class.'>');
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelAttachments(int $videoManagerId, int $channelId): ArrayCollection
    {
        $response = $this->makeRequest(
            'GET',
            sprintf('channels/%u/attachments', $channelId),
            [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]
        );

        $response = $this->normalizeGetAttachmentsResponse($response->getBody()->getContents());

        return $this->deserialize($response, 'ArrayCollection<'.Attachment::class.'>');
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords(int $videoManagerId, ?string $videoId): ArrayCollection
    {
        $uri = is_null($videoId)
            ? 'keyword/find'
            : sprintf('videos/%s/keywords', $videoId);

        $response = $this->makeRequest(
            'GET',
            $uri,
            [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]
        );

        return $this->deserialize($response->getBody()->getContents(), 'ArrayCollection<'.Keyword::class.'>');
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

    /**
     * {@inheritdoc}
     */
    public function getVideoManagers(): ArrayCollection
    {
        $response = $this->makeRequest('GET', '', []);

        return $this->deserialize($response->getBody()->getContents(), 'ArrayCollection<'.VideoManager::class.'>');
    }

    /**
     * {@inheritdoc}
     */
    public function getVideoDownloadUrls(int $videoManagerId, string $videoId): ArrayCollection
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        $response = $this->makeRequest(
            'GET',
            sprintf('videos/%s/download-urls', $videoId),
            $options
        );

        return $this->deserialize($response->getBody()->getContents(), 'ArrayCollection<'.VideoDownloadUrl::class.'>');
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
            return (new Thumbnail())->setId(intval($match[1]));
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

    /**
     * {@inheritdoc}
     */
    public function getTranscodingStatus(int $videoManagerId, string $videoId): ArrayCollection
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        $response = $this->makeRequest(
            'GET',
            'videos/'.$videoId.'/transcoding-status',
            $options
        );

        $response = $response->getBody()->getContents();

        return $this->deserialize($response, 'ArrayCollection<'.Transcode::class.'>');
    }

    public function getPlayers(int $videoManagerId): ArrayCollection
    {
        $options = [self::OPT_VIDEO_MANAGER_ID => $videoManagerId];

        $response = $this->makeRequest(
            'GET',
            'players',
            $options
        );

        $response = $response->getBody()->getContents();

        return $this->deserialize($response, 'ArrayCollection<'.Player::class.'>');
    }

    public function getCorporateTubeMetadata(int $videoManagerId, string $videoId): CorporateTubeMetaData
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        $response = $this->makeRequest(
            'GET',
            'videos/'.$videoId.'/metadata/corporate-tube',
            $options
        );

        return $this->deserialize($response->getBody()->getContents(), CorporateTubeMetaData::class);
    }

    public function updateCorporateTubeMetadata(
        int $videoManagerId,
        string $videoId,
        CorporateTubeMetaData $corporateTubeMetaData
    ): void {

        $fields = [
            'uploaderUserId' => $corporateTubeMetaData->getUploaderUserId(),
            'inChargeUserId' => $corporateTubeMetaData->getInChargeUserId(),
        ];

        if ($corporateTubeMetaData->getUploadDate()) {
            $fields['uploadDate'] = $corporateTubeMetaData->getUploadDate()->format('c');
        }

        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $fields
        ];

        $this->makeRequest(
            'PATCH',
            'videos/'.$videoId.'/metadata/corporate-tube',
            $options
        );
    }

    /**
     * @throws \Exception
     */
    public function getMetaDataSets(int $videoManagerId): ArrayCollection
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        $response = $this->makeRequest(
            'GET',
            'metadata-sets',
            $options
        );

        $data = $response->getBody()->getContents();

        /** @var ArrayCollection $metaDataSet */
        $metaDataSet = $this->deserialize($data, 'ArrayCollection<'.MetaDataSet::class.'>');

        return $metaDataSet;
    }
}
