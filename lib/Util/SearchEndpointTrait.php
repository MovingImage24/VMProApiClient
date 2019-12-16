<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Util;

use DateTime;
use MovingImage\Client\VMPro\Entity\ChannelsRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Exception;
use MovingImage\Meta\Enums\PublicationState;

/**
 * Helper methods for dealing with search endpoint.
 */
trait SearchEndpointTrait
{
    /**
     * Creates an elastic search query from the provided array od parameters.
     */
    private function createElasticSearchQuery(array $params, ?string $operator = 'AND'): string
    {
        $filteredParams = [];
        foreach ($params as $name => $value) {
            if (empty($name) || empty($value)) {
                continue;
            }

            $filteredParams[] = "$name:$value";
        }

        return implode(" $operator ", $filteredParams);
    }

    /**
     * Adjust the response from the `search` endpoint for video data type,
     * so that it is compatible with the response of `videos` endpoint.
     * Namely, it converts the date to a timestamp, adjusts the format of channels array
     * and re-maps some renamed properties (such as duration).
     * It also renames root-level properties so that they can be correctly unserialized.
     *
     * @throws Exception
     * @throws \Exception
     */
    private function normalizeSearchVideosResponse(string $response): string
    {
        $response = json_decode($response, true);
        if (!is_array($response) || !array_key_exists('result', $response) || !array_key_exists('total', $response)) {
            throw new Exception('Invalid response from search endpoint');
        }

        $response['totalCount'] = $response['total'];
        $response['videos'] = $response['result'];
        unset($response['result'], $response['total']);

        foreach ($response['videos'] as &$video) {
            $video['uploadDate'] = $video['createdDate'];
            $video['length'] = $video['duration'];
            foreach ($video as $prop => $value) {
                if (in_array($prop, ['createdDate', 'modifiedDate', 'uploadDate'])) {
                    $video[$prop] = (new DateTime($value))->getTimestamp();
                }

                if ('channels' === $prop) {
                    foreach ($value as $channelIndex => $channelId) {
                        $video[$prop][$channelIndex] = [
                            'id' => $channelId,
                            'name' => '',
                        ];
                    }
                }
            }
        }

        return json_encode($response);
    }

    /**
     * Adjust the response from the `search` endpoint for channel data type.
     * Namely, it renames the root-level properties, so they can be correctly unserialized.
     *
     * @throws Exception
     */
    private function normalizeSearchChannelsResponse(string $response): string
    {
        $response = json_decode($response, true);
        if (!is_array($response) || !array_key_exists('result', $response) || !array_key_exists('total', $response)) {
            throw new Exception('Invalid response from search endpoint');
        }

        $response['totalCount'] = $response['total'];
        $response['channels'] = $response['result'];
        unset($response['result'], $response['total']);

        return json_encode($response);
    }

    /**
     * @throws Exception
     */
    private function getTotalCountFromSearchVideosResponse(string $response): int
    {
        $response = json_decode($response, true);
        if (!is_array($response) || !array_key_exists('total', $response)) {
            throw new Exception('Response from search endpoint is missing the "total" key');
        }

        return (int) $response['total'];
    }

    private function getRequestOptionsForSearchVideosEndpoint(
        int $videoManagerId,
        ?VideosRequestParameters $parameters = null
    ): array {
        $options = [
            'documentType' => 'video',
            'videoManagerIds' => [$videoManagerId],
        ];

        $queryParams = [];

        if ($parameters) {
            $queryParams += [
                'channels' => $parameters->getChannelId(),
                'id' => $parameters->getVideoId(),
                $parameters->getSearchInField() => $parameters->getSearchTerm(),
            ];

            switch ($parameters->getPublicationState()) {
                case PublicationState::PUBLISHED:
                    $queryParams['published'] = 'true';
                    break;
                case PublicationState::NOT_PUBLISHED:
                    $queryParams['published'] = 'false';
                    break;
                //case 'all': do nothing
            }

            $options += [
                'size' => $parameters->getLimit(),
                'from' => $parameters->getOffset(),
                'orderBy' => $parameters->getOrderProperty(),
                'order' => $parameters->getOrder(),
            ];

            if ($parameters->getMetadataSetKey()) {
                $options['metaDataSetKey'] = $parameters->getMetadataSetKey();
            }
        }

        $options['query'] = $this->createElasticSearchQuery($queryParams);

        return $options;
    }

    private function getRequestOptionsForSearchChannelsEndpoint(
        int $videoManagerId,
        ChannelsRequestParameters $parameters = null
    ): array {
        $options = [
            'documentType' => 'channel',
            'videoManagerIds' => [$videoManagerId],
        ];

        $queryParams = [
            'videoManagerId' => $videoManagerId,
        ];

        if ($parameters) {
            $queryParams += [
                $parameters->getSearchInField() => $parameters->getSearchTerm(),
            ];

            $options += [
                'size' => $parameters->getLimit(),
                'from' => $parameters->getOffset(),
                'orderBy' => $parameters->getOrderProperty(),
                'order' => $parameters->getOrder(),
            ];

            if ($parameters->getMetadataSetKey()) {
                $options['metaDataSetKey'] = $parameters->getMetadataSetKey();
            }
        }

        $options['query'] = $this->createElasticSearchQuery($queryParams);

        return $options;
    }
}
