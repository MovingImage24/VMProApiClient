<?php

namespace MovingImage\VMProApiClient;

/**
 * Class ApiClientFactory
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 * @package MovingImage\VMProApiClient
 */
class ApiClientFactory
{
    public function __construct()
    {

    }

    public function create()
    {
        return new ApiClient();
    }
}