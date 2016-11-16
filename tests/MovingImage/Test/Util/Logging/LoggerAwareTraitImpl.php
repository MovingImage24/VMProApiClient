<?php

namespace MovingImage\Test\Util\Logging;

use MovingImage\Util\Logging\Traits\LoggerAwareTrait;

/**
 * Class LoggerAwareTraitImpl.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class LoggerAwareTraitImpl
{
    use LoggerAwareTrait { getLogger as public; }
}
