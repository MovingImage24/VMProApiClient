<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\ApiClient;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;
use MovingImage\Client\VMPro\Exception;
use MovingImage\Client\VMPro\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;

abstract class AbstractCoreApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const OPT_VIDEO_MANAGER_ID = 'videoManagerId';

    protected ClientInterface $httpClient;

    protected Serializer $serializer;

    public function __construct(
        ClientInterface $httpClient,
        Serializer $serializer
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    /**
     * Make a request to the API and serialize the result according to our
     * serialization strategy.
     *
     * @return object|ResponseInterface
     * @throws \Exception
     */
    protected function makeRequest(string $method, string $uri, array $options)
    {
        $logger = $this->getLogger();

        try {
            // Automagically pre-pend videoManagerId if the option is present in the
            // options for sending the request
            if (isset($options[self::OPT_VIDEO_MANAGER_ID])) {
                $uri = sprintf('%d/%s', $options[self::OPT_VIDEO_MANAGER_ID], $uri);
            }

            $logger->info(sprintf('Making API %s request to %s', $method, $uri), [$uri]);

            /** @var ResponseInterface $response */
            $response = $this->_doRequest($method, $uri, $options);

            $logger->debug('Response from HTTP call was status code:', [$response->getStatusCode()]);
            $logger->debug('Response JSON was:', [$response->getBody()]);

            return $response;
        } catch (\Exception $e) {
            throw $e; // Just rethrow for now
        }
    }

    /**
     * Perform the actual request in the implementation classes.
     *
     * @return mixed
     */
    abstract protected function _doRequest(string $method, string $uri, array $options);

    /**
     * Deserialize a response into an instance of it's associated class.
     *
     * @return object
     */
    protected function deserialize(string $data, string $serialisationClass)
    {
        return $this->serializer->deserialize($data, $serialisationClass, 'json');
    }

    /**
     * Helper method to build the JSON data array for making a request
     * with ::makeRequest(). Optional parameters with empty or null value will be
     * omitted from the return value.
     *
     * Examples:
     *
     * $this->buildJsonParameters(['title' => 'test'], ['description' => '', 'bla' => 'test'])
     *
     * Would result in:
     *
     * [
     *     'title' => 'test',
     *     'bla' => 'test',
     * ]
     *
     * @throws Exception
     */
    protected function buildJsonParameters(array $required, array $optional): array
    {
        foreach ($required as $key => $value) {
            if (empty($value)) {
                throw new Exception(sprintf('Required parameter \'%s\' is missing..', $key));
            }
        }

        $json = $required;

        foreach ($optional as $key => $value) {
            if (!empty($value) || false === $value) {
                $json[$key] = $value;
            }
        }

        return $json;
    }

    /**
     * Serializes the provided response to a string, suitable for caching.
     * The type of the $response argument varies depending on the guzzle version.
     *
     * @param  mixed  $response
     *
     * @return string
     */
    abstract protected function serializeResponse($response);

    /**
     * Unserializes the serialized response into a response object.
     * The return type varies depending on the guzzle version.
     *
     * @param  string  $serialized
     *
     * @return mixed
     *
     * @throws Exception
     */
    abstract protected function unserializeResponse($serialized);
}
