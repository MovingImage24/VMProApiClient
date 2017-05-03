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
     * @var int
     */
    private $videoManagerId;

    /**
     * Token constructor.
     *
     * @param string $tokenString
     * @param array  $tokenData
     */
    public function __construct($tokenString, array $tokenData, $videoManagerId)
    {
        $this->tokenString = $tokenString;
        $this->tokenData = $tokenData;
        $this->videoManagerId = $videoManagerId;
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
     * @return int
     */
    public function getVideoManagerId()
    {
        return $this->videoManagerId;
    }

    /**
     * @return bool
     */
    public function expired()
    {
        return $this->getTokenData()['exp'] < time();
    }
}
