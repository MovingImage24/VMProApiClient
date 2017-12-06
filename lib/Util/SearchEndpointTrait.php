<?php

namespace MovingImage\Client\VMPro\Util;

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
     *
     * @param array  $params
     * @param string $operator
     *
     * @return string
     */
    private function createElasticSearchQuery(array $params, $operator = 'AND')
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
     * @param string $response
     *
     * @return string
     *
     * @throws Exception
     */
    private function normalizeSearchVideosResponse($response)
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
                    $video[$prop] = (new \DateTime($value))->getTimestamp();
                }

                if ($prop === 'channels') {
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
     * @param string $response
     *
     * @return string
     *
     * @throws Exception
     */
    private function normalizeSearchChannelsResponse($response)
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
     * @param string $response
     *
     * @return int
     *
     * @throws Exception
     */
    private function getTotalCountFromSearchVideosResponse($response)
    {
        $response = json_decode($response, true);
        if (!is_array($response) || !array_key_exists('total', $response)) {
            throw new Exception('Response from search endpoint is missing the "total" key');
        }

        return (int) $response['total'];
    }

    /**
     * @param int                          $videoManagerId
     * @param VideosRequestParameters|null $parameters
     *
     * @return array
     */
    private function getRequestOptionsForSearchVideosEndpoint(
        $videoManagerId,
        VideosRequestParameters $parameters = null
    ) {
        $options = [
            'documentType' => 'video',
            'videoManagerIds' => [$videoManagerId],
        ];

        if ($parameters) {
            $queryParams = [
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
                'query' => $this->createElasticSearchQuery($queryParams),
            ];

            if ($parameters->getMetadataSetKey()) {
                $options['metaDataSetKey'] = $parameters->getMetadataSetKey();
            }
        }

        return $options;
    }

    /**
     * @param int                            $videoManagerId
     * @param ChannelsRequestParameters|null $parameters
     *
     * @return array
     */
    private function getRequestOptionsForSearchChannelsEndpoint(
        $videoManagerId,
        ChannelsRequestParameters $parameters = null
    ) {
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
        }

        $options['query'] = $this->createElasticSearchQuery($queryParams);

        return $options;
    }
}
