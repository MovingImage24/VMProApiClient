<?php

declare(strict_types=1);

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Entity\CorporateTubeMetaData;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetCorporateTubeMetadataTest extends ApiClientTestCase
{
    public function testGetCorporateTubeMetadata()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getCorporateTubeMetadata'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());

        $videoId = 'DCS6GW2F4nXxHkPaAwy9R_';

        $corporateTubeMetadata = $client->getCorporateTubeMetadata(1, $videoId);

        $this->assertInstanceOf(CorporateTubeMetaData::class, $corporateTubeMetadata);
        $this->assertEquals(new \DateTime('2023-06-05T14:26:23Z'), $corporateTubeMetadata->getUploadDate());
        $this->assertEquals('80076', $corporateTubeMetadata->getUploaderUserId());
        $this->assertEquals('80076', $corporateTubeMetadata->getInChargeUserId());
        $this->assertEquals('b296c90e-473c-480a-874e-f32d867b296d', $corporateTubeMetadata->getUploaderKeycloakUserId());
        $this->assertEquals('b296c90e-473c-480a-874e-f32d867b296d', $corporateTubeMetadata->getInChargeKeycloakUserId());
    }
}
