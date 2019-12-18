<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\Transcode;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetTranscodingStatusTest extends ApiClientTestCase
{
    /**
     * Assert whether a specific JSON response gets properly
     * de-serialized into an object of the right type.
     */
    public function testGetTranscodingStatusBasicOk()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getTranscodingStatus'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        /** @var ArrayCollection<Transcode> $transcodes */
        $transcodes = $client->getTranscodingStatus(2, '74WGsUCJ3QJMjN8-LeYFpm');

        $this->assertCount(2, $transcodes);
        $this->assertEquals('480p', $transcodes[0]->getQuality());
    }

    /**
     * Check if the response from VMPro is not an array.
     */
    public function testGetTranscodingStatusBasicError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Expected array, but got string.*/');
        $httpClient = $this->createMockGuzzleClient(200, [], '');

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        $client->getTranscodingStatus(2, '74WGsUCJ3QJMjN8-LeYFpm');
    }
}
