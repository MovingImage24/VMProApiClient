<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Collection\VideoCollection;
use MovingImage\Client\VMPro\Entity\Video;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetVideosTest extends ApiClientTestCase
{
    public function testGetVideos()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getVideos'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        /** @var VideoCollection $collection */
        $collection = $client->getVideos(1);

        $this->assertEquals(10, count($collection));
        $this->assertEquals('1wGJbtN7QPwkAkd8_VcgKH', $collection[0]->getId());

        /** @var Video $videoWithCorporateTubeMetadata */
        $videoWithCorporateTubeMetadata = $this->getVideoByVideoId($collection, 'E9vyH6FPkRhVNsCeHzxbA1');
        $this->assertEquals(34, $videoWithCorporateTubeMetadata->getCorporateTubeMetadata()->getInChargeUserId());
    }

    private function getVideoByVideoId(ArrayCollection $videos, $id): ?Video
    {
        /** @var Video $video */
        foreach ($videos as $video) {
            if ($video->getId() === $id) {
                return $video;
            }
        }

        return null;
    }
}
