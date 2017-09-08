<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use MovingImage\Client\VMPro\ApiClient\AbstractApiClient;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractApiClientImpl.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class AbstractApiClientImpl extends AbstractApiClient
{
    /**
     * @var mixed
     */
    private $response;

    /**
     * Returns the response provided in setResponse method - or null.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return ResponseInterface|null
     */
    public function _doRequest($method, $uri, $options)
    {
        return $this->response;
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

    /**
     * Expose this method to test it without needing reflections.
     *
     * @param array $required
     * @param array $optional
     *
     * @return array
     */
    public function buildJsonParameters(array $required, array $optional)
    {
        return parent::buildJsonParameters($required, $optional);
    }

    /**
     * Type of the $response object depends on the guzzle version.
     *
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * No-op implementation - returns whatever was passed as an argument.
     *
     * {@inheritdoc}
     *
     * @param mixed $response
     *
     * @return mixed
     */
    protected function serializeResponse($response)
    {
        return $response;
    }

    /**
     * No-op implementation - returns whatever was passed as an argument.
     *
     * {@inheritdoc}
     *
     * @param mixed $serialized
     *
     * @return mixed
     */
    protected function unserializeResponse($serialized)
    {
        return $serialized;
    }
}
