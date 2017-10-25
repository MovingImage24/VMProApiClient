<?php

namespace MovingImage\Test\Util;

use MovingImage\Client\VMPro\Util\SearchEndpointTrait;

class SearchEndpointTraitImpl
{
    use SearchEndpointTrait {
        createElasticSearchQuery as public;
        normalizeSearchChannelsResponse as public;
        normalizeSearchVideosResponse as public;
        getRequestOptionsForSearchVideosEndpoint as public;
        getTotalCountFromSearchVideosResponse as public;
    }
}
