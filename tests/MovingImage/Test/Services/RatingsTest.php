<?php

namespace MovingImage\Test\Services;

use MovingImage\Client\VMPro\ApiClient;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Services\Ratings;
use MovingImage\VMPro\TestUtil\PrivateMethodCaller;
use PHPUnit\Framework\TestCase;

/**
 * Class RatingsTest.
 *
 * @author Robert Szeker <robert.szeker@movingimage.com>
 */
class RatingsTest extends TestCase
{
    use PrivateMethodCaller;

    const RATING_AVERAGE_KEY = 'rating_value_key';
    const RATING_COUNT_KEY = 'rating_count_key';

    /** @var Ratings */
    private $ratings;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $client;

    /** @var int */
    private $vmId = 123456;

    /** @var Video */
    private $video;

    /** @var int */
    private $videoId = 123;

    public function setUp()
    {
        $this->video = new Video();
        $this->video
            ->setId($this->videoId)
            ->setCustomMetadata([]);

        $this->client = $this->createMock(ApiClient::class);

        $this->client
            ->method('getVideo')
            ->willReturn($this->video);

        $this->ratings = new Ratings($this->client, $this->vmId, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);
    }

    public function testConstructor()
    {
        $this->assertEquals(Ratings::class, get_class($this->ratings));
    }

    /**
     * Test that fetching the same video several times calls only one time the client.
     *
     * @covers \Ratings::getVideo()
     */
    public function testGetVideoFromClient()
    {
        $this->client
            ->expects($this->once())
            ->method('getVideo')
            ->willReturn($this->video);

        $this->assertEquals($this->video, $this->callMethod($this->ratings, 'getVideo', [$this->videoId]));
        $this->assertEquals($this->video, $this->callMethod($this->ratings, 'getVideo', [$this->videoId]));
    }

    /**
     * Test if storing custom meta data triggers api client and if it has been saved locally.
     * Tests also if only related custom meta data fields are saved.
     *
     * @covers \Ratings::storeCustomMetaData()
     */
    public function testStoreCustomMetaData()
    {
        $unrelatedCustomMetaDataFields = ['key' => 'value'];
        $relatedCustomMetaDataFields = [self::RATING_COUNT_KEY => 33, self::RATING_AVERAGE_KEY => 3];

        $customMetaData = array_merge($unrelatedCustomMetaDataFields, $relatedCustomMetaDataFields);

        $this->client
            ->expects($this->once())
            ->method('setCustomMetaData')
            ->with(
                $this->vmId,
                $this->videoId,
                $relatedCustomMetaDataFields
            );

        // test if custom meta data is still empty
        $this->assertEmpty($this->callMethod($this->ratings, 'getVideo', [$this->videoId])->getCustomMetaData());

        // store custom meta data
        $this->callMethod($this->ratings, 'storeCustomMetaData', [
            $customMetaData,
            $this->videoId,
        ]);

        // test if custom meta data has been set
        $this->assertEquals($customMetaData, $this->callMethod($this->ratings, 'getVideo', [$this->videoId])->getCustomMetaData());
    }

    /**
     * Test getRatingCount(). Should return 0, if custom meta data is empty.
     *
     * @covers \Ratings::getRatingCount()
     */
    public function testGetRatingCount()
    {
        // test if empty meta data field returns '0'
        $this->assertEquals(0, $this->callMethod($this->ratings, 'getRatingCount', [$this->videoId]));

        // store meta data field
        $count = 67;
        $customMetaData = [self::RATING_COUNT_KEY => $count];
        $this->callMethod($this->ratings, 'storeCustomMetaData', [$customMetaData, $this->videoId]);

        // check if rating count has been changed
        $this->assertEquals($count, $this->callMethod($this->ratings, 'getRatingCount', [$this->videoId]));
    }

    /**
     * Test getRatingAverage(). Should return 0, if custom meta data is empty.
     *
     * @covers \Ratings::getRatingAverage()
     */
    public function testGetRatingAverage()
    {
        // test if empty meta data field returns '0'
        $this->assertEquals(0, $this->ratings->getRatingAverage($this->videoId));

        // store meta data field as float
        $average = 78.2355;
        $customMetaData = [self::RATING_AVERAGE_KEY => $average];
        $this->callMethod($this->ratings, 'storeCustomMetaData', [$customMetaData, $this->videoId]);

        // check if rating average has been changed
        $this->assertEquals($average, $this->ratings->getRatingAverage($this->videoId));
    }

    /**
     * @covers \Ratings::addRating()
     */
    public function testAddRating()
    {
        $count = 2;
        $average = 2;

        $this->video
            ->setId($this->videoId)
            ->setCustomMetadata(
                [
                    self::RATING_COUNT_KEY => $count,
                    self::RATING_AVERAGE_KEY => $average,
                ]
            );

        // test initial ratings
        $this->assertEquals($count, $this->callMethod($this->ratings, 'getRatingCount', [$this->videoId]));
        $this->assertEquals($average, $this->callMethod($this->ratings, 'getRatingAverage', [$this->videoId]));

        // add new rating
        $this->callMethod($this->ratings, 'addRating', [$this->videoId, 5]);

        // test new rating
        $this->assertEquals($count + 1, $this->callMethod($this->ratings, 'getRatingCount', [$this->videoId]));
        $this->assertEquals(3, $this->callMethod($this->ratings, 'getRatingAverage', [$this->videoId]));
    }

    /**
     * @covers \Ratings::modifyRating()
     */
    public function testModifyRating()
    {
        $count = 2;
        $average = 2;

        $this->video
            ->setId($this->videoId)
            ->setCustomMetadata(
                [
                    self::RATING_COUNT_KEY => $count,
                    self::RATING_AVERAGE_KEY => $average,
                ]
            );

        // test initial ratings
        $this->assertEquals($count, $this->callMethod($this->ratings, 'getRatingCount', [$this->videoId]));
        $this->assertEquals($average, $this->callMethod($this->ratings, 'getRatingAverage', [$this->videoId]));

        // modify rating
        $this->callMethod($this->ratings, 'modifyRating', [$this->videoId, 4, 2]);

        // test new rating
        $this->assertEquals($count, $this->callMethod($this->ratings, 'getRatingCount', [$this->videoId]));
        $this->assertEquals(3, $this->callMethod($this->ratings, 'getRatingAverage', [$this->videoId]));
    }

    /**
     * @param int $rating
     * @dataProvider invalidRatingProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage rating value is not in expected range
     */
    public function testAddInvalidRating($rating)
    {
        $this->ratings->addRating(123, $rating);
    }

    /**
     * @param int $rating
     * @dataProvider invalidRatingProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage rating value is not in expected range
     */
    public function testModifyInvalidRating($rating)
    {
        $this->ratings->modifyRating(123, $rating, 3);
    }

    public function invalidRatingProvider()
    {
        return [
            [0],
            [6],
            [0.9],
            [5.1],
        ];
    }

    public function testDisabledCache()
    {
        $client = $this->createMock(ApiClient::class);
        $client
            ->expects($this->once())
            ->method('disableCaching');

        new Ratings($client, $this->vmId, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);
    }
}
