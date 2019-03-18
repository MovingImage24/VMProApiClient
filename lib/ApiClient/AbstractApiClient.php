<?php

namespace MovingImage\Client\VMPro\ApiClient;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Collection\ChannelCollection;
use MovingImage\Client\VMPro\Collection\VideoCollection;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\ChannelsRequestParameters;
use MovingImage\Client\VMPro\Entity\EmbedCode;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Entity\Attachment;
use MovingImage\Client\VMPro\Entity\VideoDownloadUrl;
use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Entity\VideoThumbnailCollection;
use MovingImage\Client\VMPro\Entity\VideoTranscodingStatus;
use MovingImage\Client\VMPro\Exception;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Client\VMPro\Util\ChannelTrait;
use MovingImage\Client\VMPro\Util\Logging\Traits\LoggerAwareTrait;
use MovingImage\Client\VMPro\Util\SearchEndpointTrait;

/**
 * Class AbstractApiClient.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 * @author Omid Rad <omid.rad@movingimage.com>
 */
abstract class AbstractApiClient extends AbstractCoreApiClient implements ApiClientInterface
{
    use LoggerAwareTrait;
    use SearchEndpointTrait;
    use ChannelTrait;

    /**
     * {@inheritdoc}
     */
    public function getChannels($videoManagerId)
    {
        $response = $this->makeRequest('GET', 'channels', [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        $rootChannel = $this->deserialize($response->getBody(), Channel::class);
        $rootChannel->setChildren($this->sortChannels($rootChannel->getChildren()));

        return $rootChannel;
    }

    /**
     * Since the VMPro API doesn't sort any more the returned channels, we have to do it on our side.
     *
     * @param ArrayCollection $channels
     *
     * @return ArrayCollection
     */
    protected function sortChannels(ArrayCollection $channels)
    {
        $channels->map(function ($channel) {
            $channel->setChildren($this->sortChannels($channel->getChildren()));
        });

        $iterator = $channels->getIterator();
        $iterator->uasort(function ($a, $b) {
            return $a->getName() > $b->getName();
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * {@inheritdoc}
     */
    public function createVideo(
        $videoManagerId,
        $fileName,
        $title = '',
        $description = '',
        $channel = null,
        $group = null,
        array $keywords = [],
        $autoPublish = null
    ) {
        $response = $this->makeRequest('POST', 'videos', [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $this->buildJsonParameters(
                compact('fileName'), // Required parameters
                compact('title', 'description', 'channel', 'group', 'keywords', 'autoPublish') // Optional parameters
            ),
        ]);

        // Guzzle 5+6 co-compatibility - Guzzle 6 for some reason
        // wraps headers in arrays.
        $videoLocation = is_array($response->getHeader('location'))
            ? $response->getHeader('location')[0]
            : $response->getHeader('location');

        $pieces = explode('/', $videoLocation);

        return end($pieces);
    }

    /**
     * {@inheritdoc}
     */
    public function getVideos($videoManagerId, VideosRequestParameters $parameters = null)
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        if ($parameters) {
            $options['query'] = $parameters->getContainer();
        }

        $response = $this->makeRequest('GET', 'videos', $options);
        $response = json_encode(json_decode($response->getBody()->getContents(), true)['videos']);

        return $this->deserialize($response, 'ArrayCollection<'.Video::class.'>');
    }

    /**
     * {@inheritdoc}
     */
    public function getCount($videoManagerId, VideosRequestParameters $parameters = null)
    {
        $options = [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ];

        if ($parameters) {
            $options['query'] = $parameters->getContainer();
        }

        $response = $this->makeRequest('GET', 'videos', $options);

        return json_decode($response->getBody()->getContents(), true)['total'];
    }

    /**
     * {@inheritdoc}
     */
    public function getVideoUploadUrl($videoManagerId, $videoId)
    {
        $response = $this->makeRequest('GET', sprintf('videos/%s/url', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        // Guzzle 5+6 co-compatibility - Guzzle 6 for some reason
        // wraps headers in arrays.
        return is_array($response->getHeader('location'))
            ? $response->getHeader('location')[0]
            : $response->getHeader('location');
    }

    /**
     * {@inheritdoc}
     */
    public function getVideoDownloadUrls($videoManagerId, $videoId)
    {
        $response = $this->makeRequest('GET', sprintf('videos/%s/download-urls', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        $response = $response->getBody()->getContents();

        return $this->deserialize($response, 'ArrayCollection<'.VideoDownloadUrl::class.'>');
    }

    /**
     * {@inheritdoc}
     */
    public function updateVideo($videoManagerId, $videoId, $title, $description)
    {
        $this->makeRequest('PATCH', sprintf('videos/%s', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $this->buildJsonParameters([], compact('title', 'description')),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function addVideoToChannel($videoManagerId, $videoId, $channelId)
    {
        $this->makeRequest('POST', sprintf('channels/%s/videos/%s', $channelId, $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomMetaData($videoManagerId, $videoId, $metadata)
    {
        $this->makeRequest('PATCH', sprintf('videos/%s/metadata', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $metadata,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbedCode($videoManagerId, $videoId, $playerDefinitionId, $embedType = 'html')
    {
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

    /**
     * {@inheritdoc}
     */
    public function deleteVideo($videoManagerId, $videoId)
    {
        $this->makeRequest('DELETE', sprintf('videos/%s', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getVideo($videoManagerId, $videoId, VideoRequestParameters $parameters = null)
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
    public function getAttachments($videoManagerId, $videoId)
    {
        $response = $this->makeRequest(
            'GET',
            sprintf('videos/%s/attachments', $videoId),
            [self::OPT_VIDEO_MANAGER_ID => $videoManagerId]
        );

        return $this->deserialize($response->getBody()->getContents(), 'ArrayCollection<'.Attachment::class.'>');
    }

    /**
     * {@inheritdoc}
     */
    public function searchVideos($videoManagerId, VideosRequestParameters $parameters = null)
    {
        $options = $this->getRequestOptionsForSearchVideosEndpoint($videoManagerId, $parameters);
        $response = $this->makeRequest('POST', 'search', ['json' => $options]);
        $response = $this->normalizeSearchVideosResponse($response->getBody()->getContents());

        $collection = $this->deserialize($response, VideoCollection::class);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function searchChannels($videoManagerId, ChannelsRequestParameters $parameters = null)
    {
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
    public function getTranscodingStatus($videoManagerId, $videoId)
    {
        $response = $this->makeRequest('GET', sprintf('videos/%s/transcoding-status', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        $response = $response->getBody()->getContents();

        return $this->deserialize($response, 'ArrayCollection<'.VideoTranscodingStatus::class.'>');
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnailCollection($videoManagerId, $videoId)
    {
        $response = $this->makeRequest('GET', sprintf('videos/%s/thumbnails', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        $response = $response->getBody()->getContents();

        return $this->deserialize($response, 'ArrayCollection<'.VideoThumbnailCollection::class.'>');
    }

    /**
     * {@inheritdoc}
     */
    public function createThumbnailForVideo($videoManagerId, $videoId)
    {
        $response = $this->makeRequest('POST', sprintf('videos/%s/thumbnails', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        return $response->getHeader('Location');
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnailUploadUrl($videoManagerId, $videoId, $thumbnailId)
    {
        $response = $this->makeRequest('GET', sprintf('videos/%s/thumbnails/%s/upload-url', $videoId, $thumbnailId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        return $response->getHeader('Location');
    }

    /**
     * {@inheritdoc}
     */
    public function publishVideoAsset($videoManagerId, $videoId)
    {
        $json = $this->buildJsonParameters(['published' => 'true'], ['downloadable' => 'true']);
        $response = $this->makeRequest('PATCH', sprintf('videos/%s', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $json
        ]);

        return $response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function updateSpecificThumbnail($videoManagerId, $videoId, $thumbnailId)
    {
        $json = $this->buildJsonParameters(['active' => 'true'], []);
        $response = $this->makeRequest('PATCH', sprintf('videos/%s/thumbnails/%s', $videoId, $thumbnailId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $json
        ]);

        return $response->getStatusCode();
    }

    /**
     * @param $videoManagerId
     * @return string
     * @throws \Exception
     */
    public function getAdConfigurations($videoManagerId)
    {
        $response = $this->makeRequest('GET', 'ad_configurations', [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $videoManagerId
     * @param $videoId
     * @param array $params
     * @return int
     * @throws \Exception
     */
    public function updateVideoData($videoManagerId, $videoId, array $params = [])
    {
        $response = $this->makeRequest('PATCH', sprintf('videos/%s', $videoId), [
            self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            'json' => $params,
        ]);

        return $response->getStatusCode();
    }
}

