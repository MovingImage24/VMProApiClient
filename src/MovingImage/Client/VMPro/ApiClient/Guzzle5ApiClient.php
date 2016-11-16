<?php

namespace MovingImage\Client\VMPro\ApiClient;

use MovingImage\Client\VMPro\ApiClient;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;

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
        $options['config']['videoManagerId'] = $options['videoManagerId'];
        unset($options['videoManagerId']);

        $request = $this->httpClient->createRequest($method, $uri, $options);

        return $this->httpClient->send($request);
    }
}
