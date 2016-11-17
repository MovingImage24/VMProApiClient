<?php

namespace MovingImage\Test\Client\VMPro\Factory;

use GuzzleHttp\ClientInterface;

class Guzzle5ApiClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (version_compare(ClientInterface::VERSION, '6.0', '>=')) {
            $this->markTestSkipped('Skipping tests for Guzzle5ApiClientFactory when Guzzle ~6.0 is installed');
        }
    }

    public function testSomething()
    {
        $this->assertTrue(true);
    }
}
