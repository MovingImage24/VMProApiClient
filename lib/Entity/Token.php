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
     */
    public function __construct($tokenString, array $tokenData)
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
     * @return bool
     */
    public function expired()
    {
        return $this->getTokenData()['exp'] < time();
    }
}
