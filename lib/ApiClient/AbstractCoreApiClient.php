<?php

namespace MovingImage\Client\VMPro\ApiClient;

use Cache\Adapter\Void\VoidCachePool;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;
use MovingImage\Client\VMPro\Exception;
use MovingImage\Client\VMPro\Interfaces\StopwatchInterface;
use MovingImage\Client\VMPro\Stopwatch\NullStopwatch;
use MovingImage\Client\VMPro\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Class AbstractCoreApiClient.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
abstract class AbstractCoreApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @const string
     */
    const OPT_VIDEO_MANAGER_ID = 'videoManagerId';

    /**
     * List of endpoints that may be cached, even though they use POST.
     */
    const CACHEABLE_POST_ENDPOINTS = ['search'];

    /**
     * @var ClientInterface The Guzzle HTTP client
     */
    protected $httpClient;

    /**
     * @var Serializer The JMS Serializer instance
     */
    protected $serializer;

    /**
     * @var CacheItemPoolInterface PSR6 cache pool implementation
     */
    protected $cacheItemPool;

    /**
     * @var mixed time-to-live for cached responses
     *            The type of this property might be integer, \DateInterval or null
     *
     * @see CacheItemInterface::expiresAfter()
     */
    protected $cacheTtl;

    /**
     * @var StopwatchInterface
     */
    protected $stopwatch;

    /**
     * ApiClient constructor.
     *
     * @param CacheItemPoolInterface $cacheItemPool
     * @param int                    $cacheTtl
     * @param StopwatchInterface     $stopwatch
     */
    public function __construct(
        ClientInterface $httpClient,
        Serializer $serializer,
        CacheItemPoolInterface $cacheItemPool = null,
        $cacheTtl = null,
        StopwatchInterface $stopwatch = null
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->cacheItemPool = $cacheItemPool ?: new VoidCachePool();
        $this->cacheTtl = $cacheTtl;
        $this->stopwatch = $stopwatch ?: new NullStopwatch();
    }

    public function setCacheItemPool(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function getCacheItemPool()
    {
        return $this->cacheItemPool;
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
     *
     * @return object|ResponseInterface
     */
    protected function makeRequest($method, $uri, $options)
    {
        $logger = $this->getLogger();

        try {
            // Automagically pre-pend videoManagerId if the option is present in the
            // options for sending the request
            if (isset($options[self::OPT_VIDEO_MANAGER_ID])) {
                $uri = sprintf('%d/%s', $options[self::OPT_VIDEO_MANAGER_ID], $uri);
            }

            $cacheKey = $this->generateCacheKey($method, $uri, $options);
            $cacheItem = $this->cacheItemPool->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                $logger->info(sprintf('Getting response from cache for %s request to %s', $method, $uri), [$uri]);

                return $this->unserializeResponse($cacheItem->get());
            }

            $logger->info(sprintf('Making API %s request to %s', $method, $uri), [$uri]);

            $stopwatchEvent = "$method-$uri";
            $this->stopwatch->start($stopwatchEvent);
            /** @var ResponseInterface $response */
            $response = $this->_doRequest($method, $uri, $options);
            $this->stopwatch->stop($stopwatchEvent);

            if ($this->isCacheable($method, $uri, $options, $response)) {
                $cacheItem->set($this->serializeResponse($response));
                $cacheItem->expiresAfter($this->cacheTtl);
                $this->cacheItemPool->save($cacheItem);
            }

            $logger->debug('Response from HTTP call was status code:', [$response->getStatusCode()]);
            $logger->debug('Response JSON was:', [$response->getBody()]);

            return $response;
        } catch (\Exception $e) {
            throw $e; // Just rethrow for now
        }
    }

    /**
     * Deserialize a response into an instance of it's associated class.
     *
     * @param string $data
     * @param string $serialisationClass
     *
     * @return object
     */
    protected function deserialize($data, $serialisationClass)
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
     * @return array
     */
    protected function buildJsonParameters(array $required, array $optional)
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
     * Generates the cache key based on the class name, request method, uri and options.
     *
     * @param string $method
     * @param string $uri
     *
     * @return string
     */
    private function generateCacheKey($method, $uri, array $options = [])
    {
        return sha1(sprintf('%s.%s.%s.%s', get_class($this), $method, $uri, json_encode($options)));
    }

    /**
     * Checks if the request may be cached.
     *
     * @param string $method
     * @param string $uri
     * @param mixed  $response
     *
     * @return bool
     */
    private function isCacheable($method, $uri, array $options, $response)
    {
        /** @var ResponseInterface $statusCode */
        $statusCode = $response->getStatusCode();

        //cache only 2** responses
        if ($statusCode < 200 || $statusCode >= 300) {
            return false;
        }

        //GET is always safe to cache
        if ('GET' === $method) {
            return true;
        }

        //POST may be cached for certain endpoints only (forgive us Roy Fielding)
        if ('POST' === $method) {
            return in_array($uri, self::CACHEABLE_POST_ENDPOINTS);
        }

        //assume not cacheable in all other cases
        return false;
    }

    /**
     * Serializes the provided response to a string, suitable for caching.
     * The type of the $response argument varies depending on the guzzle version.
     *
     * @param mixed $response
     *
     * @return string
     */
    abstract protected function serializeResponse($response);

    /**
     * Unserializes the serialized response into a response object.
     * The return type varies depending on the guzzle version.
     *
     * @param string $serialized
     *
     * @return mixed
     *
     * @throws Exception
     */
    abstract protected function unserializeResponse($serialized);
}
