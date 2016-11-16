<?php

namespace MovingImage\Client\VMPro\Entity;

/**
 * Class ApiCredentials.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
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

    /**
     * ApiCredentials constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
