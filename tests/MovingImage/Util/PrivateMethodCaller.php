<?php

namespace MovingImage\VMPro\TestUtil;

trait PrivateMethodCaller
{
    /**
     * Used to call and test private or protected methods from a given object.
     *
     * @param object $obj
     * @param string $methodName
     * @param array  $arguments
     *
     * @return mixed
     */
    public function callMethod($obj, $methodName, $arguments)
    {
        $reflector = new \ReflectionObject($obj);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invoke($obj, ...$arguments);
    }
}
