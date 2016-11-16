<?php

namespace MovingImage\Client\VMPro;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use JMS\Serializer\Serializer;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Exception\ApiException;
use MovingImage\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Class ApiClient.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class ApiClient implements LoggerAwareInterface
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
    protected function makeRequest($method, $uri, $options, $serialisationClass)
    {
        $logger = $this->getLogger();

        try {
            // Automagically replace '%videoManagerId%' with the appropriate
            // value if it' present in the options
            if (strpos($uri, '%videoManagerId%') !== false && isset($options['videoManagerId'])) {
                $uri = str_replace('%videoManagerId%', $options['videoManagerId'], $uri);
            }

            $logger->info(sprintf('Making API %s request to %s', $method, $uri), [$uri]);

            $response = $this->httpClient->request($method, $uri, $options);
            $logger->debug('Response from HTTP call was status code:', [$response->getStatusCode()]);
            $logger->debug('Response JSON was:', [$response->getBody()]);

            return $this->serializer->deserialize($response->getBody(), $serialisationClass, 'json');
        } catch (ClientException $e) {
            throw new ApiException(
                $e->getMessage(),
                $e->getCode(),
                $e,
                $e->getRequest(),
                $e->getResponse()
            );
        }
    }

    public function getChannels($videoManagerId)
    {
        return $this->makeRequest('GET', '%videoManagerId%/channels', [
            'videoManagerId' => $videoManagerId,
        ], Channel::class);
    }
}
