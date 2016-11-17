<?php

namespace MovingImage\Test\Client\VMPro\Factory;

use GuzzleHttp\ClientInterface;

class Guzzle6ApiClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '<')) {
            $this->markTestSkipped('Skipping tests for Guzzle6ApiClientFactory when Guzzle ~5.0 is installed');
        }
    }

    public function testSomething()
    {
        $this->assertTrue(true);
    }
}
