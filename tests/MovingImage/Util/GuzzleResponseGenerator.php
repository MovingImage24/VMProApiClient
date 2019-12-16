<?php

namespace MovingImage\VMPro\TestUtil;

use GuzzleHttp\Psr7\Response;

trait GuzzleResponseGenerator
{
    /**
     * @param int    $status
     * @param array  $headers
     * @param string $body
     *
     * @return Response
     */
    public function generateGuzzleResponse($status = 200, array $headers = [], $body = '')
    {
        return new Response($status, $headers, $body);
    }
}
