<?php

namespace MovingImage\Client\VMPro\ApiClient;

use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;

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
}
