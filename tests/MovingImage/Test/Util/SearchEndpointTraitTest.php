<?php

namespace MovingImage\Test\Util;

use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Exception;
use MovingImage\Client\VMPro\Util\SearchEndpointTrait;

class SearchEndpointTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SearchEndpointTraitImpl
     */
    private $traitObj;

    /**
     * Instantiate our SearchEndpointTrait implementation test helper.
     */
    public function setUp()
    {
        $this->traitObj = new SearchEndpointTraitImpl();
    }

    /**
     * @param array  $params
     * @param string $operator
     * @param string $expectedResult
     * @dataProvider dataProviderForTestCreateElasticSearchQuery
     * @covers \SearchEndpointTrait::createElasticSearchQuery()
     */
    public function testCreateElasticSearchQuery(array $params, $operator, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->traitObj->createElasticSearchQuery($params, $operator));
    }

    /**
     * Data provider for testCreateElasticSearchQuery.
     *
     * @return array
     */
    public function dataProviderForTestCreateElasticSearchQuery()
    {
        return [
            [['name' => 'Name', 'desc' => 'Desc'], 'AND', 'name:Name AND desc:Desc'],
            [['name' => 'Name', 'desc' => 'Desc'], 'OR', 'name:Name OR desc:Desc'],
        ];
    }

    /**
     * @covers \SearchEndpointTrait::normalizeSearchVideosResponse()
     */
    public function testNormalizeSearchVideosResponse()
    {
        $response = [
            'total' => 10,
            'result' => [[
                'videoManagerId' => 319,
                'id' => '1wGJbtN7QPwkAkd8_VcgKH',
                'description' => '',
                'title' => 'Bienvenue',
                'uploadFileName' => 'RB_logo3_1080p.mov',
                'duration' => 4120,
                'createdDate' => '2017-09-07T13:20:19Z',
                'published' => true,
                'downloadable' => false,
                'keywords' => [],
                'customMetadata' => [
                    'language' => [
                        'fr',
                    ],
                ],
                'chapters' => [],
                'overlays' => [],
                'channels' => [
                    34003,
                    4396,
                ],
            ]],
        ];
        $normalizedResponse = $this->traitObj->normalizeSearchVideosResponse(json_encode($response));
        $normalizedResponse = json_decode($normalizedResponse, true);
        $timestamp = date_create($response['result'][0]['createdDate'])->getTimestamp();

        $this->assertArrayHasKey('videos', $normalizedResponse);
        $this->assertArrayHasKey('totalCount', $normalizedResponse);
        $this->assertSame($timestamp, $normalizedResponse['videos'][0]['createdDate']);
        $this->assertSame($timestamp, $normalizedResponse['videos'][0]['uploadDate']);
        $this->assertSame($response['result'][0]['duration'], $normalizedResponse['videos'][0]['length']);
        foreach ($normalizedResponse['videos'][0]['channels'] as $channel) {
            $this->assertArrayHasKey('id', $channel);
            $this->assertArrayHasKey('name', $channel);
        }
    }

    /**
     * @covers \SearchEndpointTrait::normalizeSearchVideosResponse()
     */
    public function testNormalizeSearchVideosResponseWithInvalidInput()
    {
        $this->expectException(Exception::class);
        $this->traitObj->normalizeSearchVideosResponse('');
    }

    /**
     * @covers \SearchEndpointTrait::normalizeSearchChannelsResponse()
     */
    public function testNormalizeSearchChannelsResponse()
    {
        $response = [
            'total' => 10,
            'result' => 'channels',
        ];

        $normalizedResponse = $this->traitObj->normalizeSearchChannelsResponse(json_encode($response));
        $normalizedResponse = json_decode($normalizedResponse, true);
        $this->assertSame(10, $normalizedResponse['totalCount']);
        $this->assertSame('channels', $normalizedResponse['channels']);
    }

    /**
     * @covers \SearchEndpointTrait::normalizeSearchChannelsResponse()
     */
    public function testNormalizeSearchChannelsResponseWithInvalidInput()
    {
        $this->expectException(Exception::class);
        $this->traitObj->normalizeSearchChannelsResponse('');
    }

    /**
     * @param array $params
     * @covers \SearchEndpointTrait::getRequestOptionsForSearchVideosEndpoint()
     * @dataProvider dataProviderForTestGetRequestOptionsForSearchVideosEndpoint
     */
    public function testGetRequestOptionsForSearchVideosEndpoint(array $params)
    {
        $params = $this->createVideosRequestParameters($params);
        $vmId = 42;
        $expectedFetchSources = [
            'id',
            'title',
            'description',
            'createdDate',
            'duration',
            'published',
            'customMetadata',
            'keywords',
            'channels',
            'downloadable',
        ];

        $options = $this->traitObj->getRequestOptionsForSearchVideosEndpoint($vmId, $params);

        $this->assertArrayHasKey('documentType', $options);
        $this->assertSame('video', $options['documentType']);
        $this->assertArrayHasKey('videoManagerIds', $options);
        $this->assertSame([$vmId], $options['videoManagerIds']);
        $this->assertArrayHasKey('fetchSources', $options);
        $this->assertSame($expectedFetchSources, $options['fetchSources']);

        if ($params->getLimit()) {
            $this->assertArrayHasKey('size', $options);
            $this->assertSame($params->getLimit(), $options['size']);
        }

        if ($params->getOffset()) {
            $this->assertArrayHasKey('from', $options);
            $this->assertSame($params->getOffset(), $options['from']);
        }

        if ($params->getOrderProperty()) {
            $this->assertArrayHasKey('orderBy', $options);
            $this->assertSame($params->getOrderProperty(), $options['orderBy']);
        }

        if ($params->getOrder()) {
            $this->assertArrayHasKey('order', $options);
            $this->assertSame($params->getOrder(), $options['order']);
        }

        $query = [
            'channels' => $params->getChannelId(),
        ];

        if ($params->getPublicationState() !== null) {
            $query['published'] = $params->getPublicationState();
        }

        if ($params->getSearchInField() && $params->getSearchTerm()) {
            $query[$params->getSearchInField()] = $params->getSearchTerm();
        }

        //this method is tested separately
        $query = $this->traitObj->createElasticSearchQuery($query);

        $this->assertArrayHasKey('query', $options);
        $this->assertSame($query, $options['query']);
    }

    /**
     * Data provider for testGetRequestOptionsForSearchVideosEndpoint.
     *
     * @return array
     */
    public function dataProviderForTestGetRequestOptionsForSearchVideosEndpoint()
    {
        return [
            [[]],
            [['channelId' => 100]],
            [['order' => 'desc', 'orderProperty' => 'name']],
            [['limit' => 10, 'offset' => 20]],
            [['publicationState' => false]],
            [['publicationState' => true]],
            [['searchTerm' => 'search', 'searchInField' => 'name']],
        ];
    }

    /**
     * @covers \SearchEndpointTrait::getTotalCountFromSearchVideosResponse()
     */
    public function testGetTotalCountFromSearchVideosResponse()
    {
        $response = [
            'total' => 100,
        ];

        $total = $this->traitObj->getTotalCountFromSearchVideosResponse(json_encode($response));
        $this->assertSame(100, $total);
    }

    /**
     * @covers \SearchEndpointTrait::getTotalCountFromSearchVideosResponse()
     */
    public function testGetTotalCountFromSearchVideosResponseWithInvalidInput()
    {
        $this->expectException(Exception::class);
        $this->traitObj->getTotalCountFromSearchVideosResponse('');
    }

    /**
     * Creates a VideosRequestParameters object from the provided array.
     *
     * @param array $arrayParams
     *
     * @return VideosRequestParameters
     */
    private function createVideosRequestParameters(array $arrayParams)
    {
        $parameters = new VideosRequestParameters();
        foreach ($arrayParams as $param => $value) {
            $setter = 'set'.ucfirst($param);
            call_user_func([$parameters, $setter], $value);
        }

        return $parameters;
    }
}
