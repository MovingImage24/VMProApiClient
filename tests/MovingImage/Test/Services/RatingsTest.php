<?php

namespace MovingImage\Test\Services;

use GuzzleHttp\Client;
use JMS\Serializer\SerializerBuilder;
use MovingImage\Client\VMPro\ApiClient;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Client\VMPro\Services\Ratings;
use MovingImage\Client\VMPro\Util\PrivateMethodCaller;
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

    public function testConstructor()
    {
        $httpClient = new Client();
        $serializer = SerializerBuilder::create()->build();

        $rating = new Ratings(new ApiClient($httpClient, $serializer), 1, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);
        $this->assertEquals(Ratings::class, get_class($rating));
    }

    /**
     * Test that fetching the same video several times calls only one time the client.
     *
     * @covers \Ratings::getVideo()
     */
    public function testGetVideoFromClient()
    {
        $count = 2;
        $average = 3;
        $videoId = 123;

        $video = new Video();
        $video
            ->setId($videoId)
            ->setCustomMetadata(
                [
                    self::RATING_AVERAGE_KEY => $average,
                    self::RATING_COUNT_KEY => $count,
                ]
            );

        $client = $this->createMock(ApiClient::class);
        $client
            ->expects($this->once())
            ->method('getVideo')
            ->willReturn($video);

        $ratings = new Ratings($client, 123, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);

        $this->assertEquals($video, $this->callMethod($ratings, 'getVideo', [$videoId]));
        $this->assertEquals($video, $this->callMethod($ratings, 'getVideo', [$videoId]));
    }

    /**
     * Test if storing custom meta data triggers api client and if it has been saved locally.
     *
     * @covers \Ratings::storeCustomMetaData()
     */
    public function testStoreCustomMetaData()
    {
        $videoId = 123;
        $vmId = 12345;

        $video = new Video();
        $video
            ->setId($videoId)
            ->setCustomMetadata([]);

        $client = $this->createMock(ApiClient::class);
        $client
            ->expects($this->once())
            ->method('getVideo')
            ->willReturn($video);

        $customMetaData = ['key' => 'value'];

        $client
            ->expects($this->once())
            ->method('setCustomMetaData')
            ->with(
                $vmId,
                $videoId,
                $customMetaData
            );

        $ratings = new Ratings($client, $vmId, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);

        // test if custom meta data is still empty
        $this->assertEmpty($this->callMethod($ratings, 'getVideo', [$videoId])->getCustomMetaData());

        // store custom meta data
        $this->callMethod($ratings, 'storeCustomMetaData', [
            $customMetaData,
            $videoId,
        ]);

        // test if custom meta data has been set
        $this->assertEquals($customMetaData, $this->callMethod($ratings, 'getVideo', [$videoId])->getCustomMetaData());
    }

    /**
     * Test getRatingCount(). Should return 0, if custom meta data is empty.
     *
     * @covers \Ratings::getRatingCount()
     */
    public function testGetRatingCount()
    {
        $videoId = 123;

        $video = new Video();
        $video
            ->setId($videoId)
            ->setCustomMetadata([]);

        $client = $this->createMock(ApiClient::class);
        $client
            ->expects($this->once())
            ->method('getVideo')
            ->willReturn($video);

        $ratings = new Ratings($client, 12345, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);

        // test if empty meta data field returns '0'
        $this->assertEquals(0, $this->callMethod($ratings, 'getRatingCount', [$videoId]));

        // store meta data field
        $count = 67;
        $customMetaData = [self::RATING_COUNT_KEY => $count];
        $this->callMethod($ratings, 'storeCustomMetaData', [$customMetaData, $videoId]);

        // check if rating count has been changed
        $this->assertEquals($count, $this->callMethod($ratings, 'getRatingCount', [$videoId]));
    }

    /**
     * Test getRatingAverage(). Should return 0, if custom meta data is empty.
     *
     * @covers \Ratings::getRatingAverage()
     */
    public function testGetRatingAverage()
    {
        $videoId = 123;

        $video = new Video();
        $video
            ->setId($videoId)
            ->setCustomMetadata([]);

        $client = $this->createMock(ApiClient::class);
        $client
            ->expects($this->once())
            ->method('getVideo')
            ->willReturn($video);

        $ratings = new Ratings($client, 12345, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);

        // test if empty meta data field returns '0'
        $this->assertEquals(0, $this->callMethod($ratings, 'getRatingAverage', [$videoId]));

        // store meta data field
        $average = 78;
        $customMetaData = [self::RATING_AVERAGE_KEY => $average];
        $this->callMethod($ratings, 'storeCustomMetaData', [$customMetaData, $videoId]);

        // check if rating average has been changed
        $this->assertEquals($average, $this->callMethod($ratings, 'getRatingAverage', [$videoId]));
    }

    /**
     * @covers \Ratings::addRating()
     */
    public function testAddRating()
    {
        $count = 2;
        $average = 2;
        $videoId = 123;

        $video = new Video();
        $video
            ->setId($videoId)
            ->setCustomMetadata(
                [
                    self::RATING_COUNT_KEY => $count,
                    self::RATING_AVERAGE_KEY => $average,
                ]
            );

        $client = $this->createMock(ApiClient::class);
        $client
            ->expects($this->once())
            ->method('getVideo')
            ->willReturn($video);

        $ratings = new Ratings($client, 12345, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);

        // test initial ratings
        $this->assertEquals($count, $this->callMethod($ratings, 'getRatingCount', [$videoId]));
        $this->assertEquals($average, $this->callMethod($ratings, 'getRatingAverage', [$videoId]));

        // add new rating
        $this->callMethod($ratings, 'addRating', [$videoId, 5]);

        // test new rating
        $this->assertEquals($count + 1, $this->callMethod($ratings, 'getRatingCount', [$videoId]));
        $this->assertEquals(3, $this->callMethod($ratings, 'getRatingAverage', [$videoId]));
    }

    /**
     * @covers \Ratings::modifyRating()
     */
    public function testModifyRating()
    {
        $count = 2;
        $average = 2;
        $videoId = 123;

        $video = new Video();
        $video
            ->setId($videoId)
            ->setCustomMetadata(
                [
                    self::RATING_COUNT_KEY => $count,
                    self::RATING_AVERAGE_KEY => $average,
                ]
            );

        $client = $this->createMock(ApiClient::class);
        $client
            ->expects($this->once())
            ->method('getVideo')
            ->willReturn($video);

        $ratings = new Ratings($client, 12345, self::RATING_AVERAGE_KEY, self::RATING_COUNT_KEY);

        // test initial ratings
        $this->assertEquals($count, $this->callMethod($ratings, 'getRatingCount', [$videoId]));
        $this->assertEquals($average, $this->callMethod($ratings, 'getRatingAverage', [$videoId]));

        // modify rating
        $this->callMethod($ratings, 'modifyRating', [$videoId, 4, 2]);

        // test new rating
        $this->assertEquals($count, $this->callMethod($ratings, 'getRatingCount', [$videoId]));
        $this->assertEquals(3, $this->callMethod($ratings, 'getRatingAverage', [$videoId]));
    }
}
