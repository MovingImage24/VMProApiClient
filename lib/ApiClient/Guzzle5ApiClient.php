<?php

namespace MovingImage\Client\VMPro\ApiClient;

use GuzzleHttp\Message\Response;
use MovingImage\Client\VMPro\ApiClient;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use MovingImage\Client\VMPro\Exception;
use GuzzleHttp\Stream\Stream;

/**
 * Class Guzzle5ApiClient.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class Guzzle5ApiClient extends ApiClient implements ApiClientInterface
{
    /**
     * Guzzle5 Client implementation for making HTTP requests with
     * the appropriate options.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    protected function _doRequest($method, $uri, $options)
    {
        // For Guzzle5 we cannot have any options that are not pre-defined,
        // so instead we put it in the config array
        if (isset($options[self::OPT_VIDEO_MANAGER_ID])) {
            $options['config'][self::OPT_VIDEO_MANAGER_ID] = $options[self::OPT_VIDEO_MANAGER_ID];
            unset($options[self::OPT_VIDEO_MANAGER_ID]);
        }

        $request = $this->httpClient->createRequest($method, $uri, $options);

        return $this->httpClient->send($request);
    }

    /**
     * {@inheritdoc}
     *
     * @param ResponseInterface $response
     *
     * @return string
     */
    protected function serializeResponse($response)
    {
        /** @var ResponseInterface $response */
        $serialized = serialize([
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody()->getContents(),
        ]);

        //subsequent calls need to access the stream from the beginning
        $response->getBody()->seek(0);

        return $serialized;
    }

    /**
     * Unserializes the serialized response into a ResponseInterface instance.
     *
     * @param string $serialized
     *
     * @return ResponseInterface
     *
     * @throws Exception
     */
    protected function unserializeResponse($serialized)
    {
        $array = unserialize($serialized);
        if (!is_array($array) || 3 !== count($array)) {
            throw new Exception(sprintf('Error unserializing response: %s', $serialized));
        }

        return new Response($array[0], $array[1], Stream::factory($array[2]));
    }
}
