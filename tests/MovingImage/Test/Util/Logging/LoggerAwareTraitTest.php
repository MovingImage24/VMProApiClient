<?php

namespace MovingImage\Test\Util\Logging;

use Monolog\Logger;

/**
 * Class LoggerAwareTraitTest.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class LoggerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoggerAwareTraitImpl
     */
    private $traitObj;

    /**
     * Instantiate our logger aware trait implementation test helper.
     */
    public function setUp()
    {
        $this->traitObj = new LoggerAwareTraitImpl();
    }

    /**
     * Test whether the instance that comes rolling out of getLogger() is
     * the exact same instance we injected into the logger aware trait impl class.
     */
    public function testLoggerAwareness()
    {
        $logger = new Logger('test');
        $this->traitObj->setLogger($logger);

        $this->assertEquals($logger, $this->traitObj->getLogger());
    }
}
