<?php

namespace MovingImage\Test\Client\VMPro\Factory;

use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Factory\AbstractApiClientFactory;
use MovingImage\Test\Client\VMPro\ApiClient\AbstractApiClientImpl;

/**
 * Class AbstractApiClientFactoryImpl.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class AbstractApiClientFactoryImpl extends AbstractApiClientFactory
{
    /**
     * Return our impl class for testing.
     *
     * @return string
     */
    protected function getApiClientClass()
    {
        return AbstractApiClientImpl::class;
    }

    /**
     * @return string
     */
    protected function getGuzzleBaseUriOptionKey()
    {
        return 'base_uri';
    }

    public function createSimple($baseUri, ApiCredentials $credentials, $authUrl)
    {
    }
}
