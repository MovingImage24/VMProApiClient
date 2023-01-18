<?php

declare(strict_types=1);

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\MetaDataSet;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetMetaDataSetsTest extends ApiClientTestCase
{
    public function testGetMetaDataSets()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getMetaDataSets'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());

        $metaDataSets = $client->getMetaDataSets(1);

        /** @var MetaDataSet $firstMetaDataSet */
        $firstMetaDataSet = $metaDataSets[0];
        $secondMetaDataSet = $metaDataSets[1];
        $thirdMetaDataSet = $metaDataSets[2];

        $this->assertInstanceOf(ArrayCollection::class, $metaDataSets);
        $this->assertCount(3, $metaDataSets);

        $this->assertInstanceOf(MetaDataSet::class, $firstMetaDataSet);
        $this->assertEquals(526, $firstMetaDataSet->getId());
        $this->assertEquals('en', $firstMetaDataSet->getKeyName());
        $this->assertEquals('LOCALIZATION', $firstMetaDataSet->getType());
        $this->assertTrue($firstMetaDataSet->isDefault());

        $this->assertInstanceOf(MetaDataSet::class, $secondMetaDataSet);
        $this->assertEquals(1674, $secondMetaDataSet->getId());
        $this->assertEquals('en-AU', $secondMetaDataSet->getKeyName());
        $this->assertEquals('LOCALIZATION', $secondMetaDataSet->getType());
        $this->assertFalse($secondMetaDataSet->isDefault());

        $this->assertInstanceOf(MetaDataSet::class, $thirdMetaDataSet);
        $this->assertEquals(1951, $thirdMetaDataSet->getId());
        $this->assertEquals('de', $thirdMetaDataSet->getKeyName());
        $this->assertEquals('LOCALIZATION', $thirdMetaDataSet->getType());
        $this->assertFalse($thirdMetaDataSet->isDefault());
    }
}
