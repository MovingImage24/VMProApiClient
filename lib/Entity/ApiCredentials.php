<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

class ApiCredentials
{
    /**
     * @var string VMPro API username
     */
    private $username;

    /**
     * @var string VMPro API password
     */
    private $password;

    public function __construct(?string $username, ?string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
