<?php

declare(strict_types=1);

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

    public function __construct(string $tokenString, array $tokenData)
    {
        $this->tokenString = $tokenString;
        $this->tokenData = $tokenData;
    }

    public function getTokenString(): string
    {
        return $this->tokenString;
    }

    public function getTokenData(): array
    {
        return $this->tokenData;
    }

    public function expired(): bool
    {
        return $this->getTokenData()['exp'] < time();
    }
}
