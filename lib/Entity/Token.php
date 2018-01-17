<?php

namespace MovingImage\Client\VMPro\Entity;

class Token
{
    /**
     * @var string
     */
    private $tokenString;

    /**
     * @var array
     */
    private $tokenData;

    /**
     * Token constructor.
     *
     * @param string $tokenString
     * @param array  $tokenData
     * @param int    $videoManagerId - deprecated, kept for BC
     */
    public function __construct($tokenString, array $tokenData, $videoManagerId = null)
    {
        $this->tokenString = $tokenString;
        $this->tokenData = $tokenData;
    }

    /**
     * @return string
     */
    public function getTokenString()
    {
        return $this->tokenString;
    }

    /**
     * @return array
     */
    public function getTokenData()
    {
        return $this->tokenData;
    }

    /**
     * This method is kept for BC and will be removed in the future.
     *
     * @deprecated
     *
     * @return int
     */
    public function getVideoManagerId()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function expired()
    {
        return $this->getTokenData()['exp'] < time();
    }
}
