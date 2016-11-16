<?php

namespace MovingImage\Client\VMPro\Exception;

use MovingImage\Client\VMPro\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiException.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class ApiException extends Exception
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * ApiException constructor.
     *
     * @param string            $message
     * @param int               $code
     * @param \Exception        $previous
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     */
    public function __construct(
        $message,
        $code,
        \Exception $previous,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->request = $request;
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
