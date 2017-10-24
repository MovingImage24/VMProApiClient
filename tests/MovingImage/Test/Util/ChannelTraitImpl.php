<?php

namespace MovingImage\Test\Util;

use MovingImage\Client\VMPro\Util\ChannelTrait;

class ChannelTraitImpl
{
    use ChannelTrait {
        setChannelRelations as public;
    }
}
