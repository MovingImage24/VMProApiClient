<?php

declare(strict_types=1);

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Entity\CorporateTubeMetaData;
use MovingImage\TestCase\ApiClientTestCase;

class UpdateCorporateTubeMetadataTest extends ApiClientTestCase
{
    public function testUpdateCorporateTubeMetadata()
    {
        $httpClient = $this->createMockGuzzleClient(204);

        $client = $this->createApiClient($httpClient, $this->createSerializer());

        $videoId = 'DCS6GW2F4nXxHkPaAwy9R_';
        $userId = '80076';
        $keycloakUserId = 'b296c90e-473c-480a-874e-f32d867b296d';
        $uploadDate = new \DateTime('2023-06-05T14:26:23Z');

        $corporateTubeMetadata = new CorporateTubeMetaData();
        $corporateTubeMetadata
            ->setUploaderUserId($userId)
            ->setInChargeUserId($userId)
            ->setUploaderKeycloakUserId($keycloakUserId)
            ->setInChargeKeycloakUserId($keycloakUserId)
            ->setUploadDate($uploadDate);

        $client->updateCorporateTubeMetadata(1, $videoId, $corporateTubeMetadata);

        $params = json_decode($this->getLastRequest()->getBody()->getContents(), true);

        $this->assertArrayHasKey('uploadDate', $params);
        $this->assertEquals($uploadDate, new \DateTime($params['uploadDate']));

        $this->assertArrayHasKey('uploaderUserId', $params);
        $this->assertEquals($userId, $params['uploaderUserId']);
        $this->assertArrayHasKey('inChargeUserId', $params);
        $this->assertEquals($userId, $params['inChargeUserId']);

        $this->assertArrayHasKey('uploaderKeycloakUserId', $params);
        $this->assertEquals($keycloakUserId, $params['uploaderKeycloakUserId']);
        $this->assertArrayHasKey('inChargeKeycloakUserId', $params);
        $this->assertEquals($keycloakUserId, $params['inChargeKeycloakUserId']);
    }
}
