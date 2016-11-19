<?php

namespace MovingImage\Test\Client\VMPro\ApiClient;

use GuzzleHttp\ClientInterface;

class Guzzle5ApiClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '>=')) {
            $this->markTestSkipped('Skipping tests for Guzzle5ApiClient when Guzzle ~6.0 is installed');
        }
    }

    public function testSomething()
    {
        $this->assertTrue(true);
    }
}
