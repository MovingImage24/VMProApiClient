<?php

namespace MovingImage\Client\VMPro\ApiClient;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Class AbstractApiClient.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
abstract class AbstractApiClient implements
    ApiClientInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var ClientInterface The Guzzle HTTP client
     */
    protected $httpClient;

    /**
     * @var Serializer The JMS Serializer instance
     */
    protected $serializer;

    /**
     * ApiClient constructor.
     *
     * @param ClientInterface $httpClient
     * @param Serializer      $serializer
     */
    public function __construct(
        ClientInterface $httpClient,
        Serializer $serializer
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    /**
     * Perform the actual request in the implementation classes.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return mixed
     */
    abstract protected function _doRequest($method, $uri, $options);

    /**
     * Make a request to the API and serialize the result according to our
     * serialization strategy.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     * @param object $serialisationClass
     *
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    protected function makeRequest($method, $uri, $options, $serialisationClass = null)
    {
        $logger = $this->getLogger();

        try {
            // Automagically replace '%videoManagerId%' with the appropriate
            // value if it' present in the options
            if (strpos($uri, '%videoManagerId%') !== false && isset($options['videoManagerId'])) {
                $uri = str_replace('%videoManagerId%', $options['videoManagerId'], $uri);
            }

            $logger->info(sprintf('Making API %s request to %s', $method, $uri), [$uri]);

            $response = $this->_doRequest($method, $uri, $options);

            $logger->debug('Response from HTTP call was status code:', [$response->getStatusCode()]);
            $logger->debug('Response JSON was:', [$response->getBody()]);

            if (!is_null($serialisationClass)) {
                return $this->serializer->deserialize($response->getBody(), $serialisationClass, 'json');
            } else {
                return \json_decode($response->getBody());
            }
        } catch (\Exception $e) {
            throw $e; // Just rethrow for now
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels($videoManagerId)
    {
        return $this->makeRequest('GET', '%videoManagerId%/channels', [
            'videoManagerId' => $videoManagerId,
        ], Channel::class);
    }
}
