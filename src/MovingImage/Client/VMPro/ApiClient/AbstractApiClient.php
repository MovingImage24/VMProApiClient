<?php

namespace MovingImage\Client\VMPro\ApiClient;

use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Entity\VideoDownloadUrl;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Util\Logging\Traits\LoggerAwareTrait;

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

        return $this->deserialize($response->getBody(), Channel::class);
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
        $response = $this->makeRequest('GET',
            sprintf('videos/%s/embed-codes?player_definition_id=%s&embed_type=%s',
                $videoId, $playerDefinitionId, $embedType), [
                self::OPT_VIDEO_MANAGER_ID => $videoManagerId,
            ]
        );

        $data = \json_decode($response->getBody(), true);

        return $data['embedCode'];
    }
}
