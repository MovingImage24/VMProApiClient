<?php

namespace MovingImage\Client\VMPro\ApiClient;

use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Util\Logging\Traits\LoggerAwareTrait;

/**
 * Class AbstractApiClient.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
abstract class AbstractApiClient extends AbstractCoreApiClient implements ApiClientInterface
{
    use LoggerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getChannels($videoManagerId)
    {
        $response = $this->makeRequest('GET', '%videoManagerId%/channels', [
            'videoManagerId' => $videoManagerId,
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
        $autoPublish = true
    ) {
        $response = $this->makeRequest('POST', '%videoManagerId%/videos', [
            'videoManagerId' => $videoManagerId,
            'json' => $this->buildJsonParameters(
                compact('fileName'), // Required parameters
                compact('title', 'description', 'channel', 'group', 'keywords', 'autoPublish') // Optional parameters
            ),
        ]);

        $videoLocation = $response->getHeader('location')[0];
        $pieces = explode('/', $videoLocation);

        return $pieces[count($pieces) - 1];
    }

    /**
     * {@inheritdoc}
     */
    public function getVideoUploadUrl($videoManagerId, $videoId)
    {
        $response = $this->makeRequest('GET', sprintf('%%videoManagerId%%/videos/%s/url', $videoId), [
            'videoManagerId' => $videoManagerId,
        ]);

        return $response->getHeader('location')[0];
    }
}
