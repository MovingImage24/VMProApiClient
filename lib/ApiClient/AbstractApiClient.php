<?php

namespace MovingImage\Client\VMPro\ApiClient;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\EmbedCode;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Client\VMPro\Util\Logging\Traits\LoggerAwareTrait;

/**
 * Class AbstractApiClient.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 * @author Omid Rad <omid.rad@movingimage.com>
 */
abstract class AbstractApiClient extends AbstractCoreApiClient implements ApiClientInterface
{
    use LoggerAwareTrait;

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
        $response = $this->makeRequest('GET',
            sprintf('videos/%s/embed-codes?player_definition_id=%s&embed_type=%s',
                $videoId, $playerDefinitionId, $embedType), [
                self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            ]
        );

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
}
