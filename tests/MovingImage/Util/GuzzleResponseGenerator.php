<?php

namespace MovingImage\VMPro\TestUtil;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Stream\Stream;

trait GuzzleResponseGenerator
{
    /**
     * @param int    $status
     * @param string $body
     *
     * @return \GuzzleHttp\Psr7\Response|\GuzzleHttp\Message\Response
     */
    public function generateGuzzleResponse($status = 200, array $headers = [], $body = '')
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '>=')) {
            return new \GuzzleHttp\Psr7\Response($status, $headers, $body);
        } else {
            return new \GuzzleHttp\Message\Response($status, $headers, Stream::factory($body));
        }
    }
}
