<?php

namespace MovingImage\Client\VMPro\ApiClient;

use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use MovingImage\Client\VMPro\Exception;

/**
 * Class Guzzle6ApiClient.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class Guzzle6ApiClient extends AbstractApiClient implements ApiClientInterface
{
    /**
     * Guzzle6 Client implementation for making HTTP requests with
     * the appropriate options.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return mixed
     */
    protected function _doRequest($method, $uri, $options)
    {
        return $this->httpClient->request($method, $uri, $options);
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
        $response->getBody()->rewind();

        return $serialized;
    }

    /**
     * {@inheritdoc}
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
        if (!is_array($array) || count($array) !== 3) {
            throw new Exception(sprintf('Error unserializing response: %s', $serialized));
        }

        return new Response($array[0], $array[1], $array[2]);
    }
}
