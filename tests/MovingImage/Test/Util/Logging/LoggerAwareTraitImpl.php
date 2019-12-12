<?php

namespace MovingImage\Test\Util\Logging;

use MovingImage\Client\VMPro\Util\Logging\Traits\LoggerAwareTrait;

class LoggerAwareTraitImpl
{
    use LoggerAwareTrait { getLogger as public; }
}
