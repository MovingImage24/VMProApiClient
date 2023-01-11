<?php

declare(strict_types=1);

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\MetaDataSet;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetMetaDataSets extends ApiClientTestCase
{
    public function testGetMetaDataSets()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getMetaDataSets'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());

        $collection = $client->getMetaDataSets(1);
        $metaDataSets = $collection->getMetaDataSets();

        /** @var MetaDataSet $firstMetaDataSet */
        $firstMetaDataSet = $metaDataSets[0];

        $this->assertInstanceOf(ArrayCollection::class, $metaDataSets);
        $this->assertCount(1, $metaDataSets);
        $this->assertInstanceOf(MetaDataSet::class, $firstMetaDataSet);
        $this->assertEquals(1044, $firstMetaDataSet->getId());
        $this->assertEquals('en-US', $firstMetaDataSet->getKeyName());
        $this->assertEquals('LOCALIZATION', $firstMetaDataSet->getType());
        $this->assertTrue($firstMetaDataSet->isDefault());
    }
}
