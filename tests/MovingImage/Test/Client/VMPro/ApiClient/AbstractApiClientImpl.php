<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use MovingImage\Client\VMPro\ApiClient\AbstractApiClient;

/**
 * Class AbstractApiClientImpl.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class AbstractApiClientImpl extends AbstractApiClient
{
    /**
     * Actually do nothing here.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     */
    public function _doRequest($method, $uri, $options)
    {
    }

    /**
     * Expose our LoggerInterface instance to do assertions with.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return parent::getLogger();
    }
}
