<?php

namespace MovingImage\Test\Client\VMPro\Factory;

use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Factory\AbstractApiClientFactory;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Test\Client\VMPro\ApiClient\AbstractApiClientImpl;

class AbstractApiClientFactoryImpl extends AbstractApiClientFactory
{
    /**
     * Return our impl class for testing.
     */
    protected function getApiClientClass(): string
    {
        return AbstractApiClientImpl::class;
    }

    protected function getGuzzleBaseUriOptionKey(): string
    {
        return 'base_uri';
    }

    public function createSimple($baseUri, ApiCredentials $credentials, $authUrl): ApiClientInterface
    {
    }
}
