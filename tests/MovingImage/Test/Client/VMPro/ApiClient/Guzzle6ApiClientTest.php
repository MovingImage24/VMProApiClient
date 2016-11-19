<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\ClientInterface;

class Guzzle6ApiClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '<')) {
            $this->markTestSkipped('Skipping tests for Guzzle6ApiClient when Guzzle ~5.0 is installed');
        }
    }

    public function testSomething()
    {
        $this->assertTrue(true);
    }
}
