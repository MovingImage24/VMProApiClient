<?php

namespace MovingImage\VMProApiClient\Util;

/**
 * AccessorTrait for general purposes.
 *
 * @author Omid Rad <omid.rad@movingimage.com>
 */
trait AccessorTrait
{
    public static $typeSnakeCase = 0;
    public static $typeCamelCase = 1;

    /**
     * @var int Set default type to snake case
     */
    private $type = 0;

    private $container = [];

    /**
     * @param $methodName string Key
     * @param $args       array  Method Arguments
     *
     * @return mixed
     */
    public function __call($methodName, $args)
    {
        // are we getting or setting?
        if (preg_match('~^(set|get|is)([A-Z])(.*)$~', $methodName, $matches)) {
            $property = strtolower($matches[2]).$matches[3];

            if ($this->type === self::$typeSnakeCase) {
                $property = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property));
            }

            switch ($matches[1]) {
                case 'set':
                    $this->checkArguments($args, 1, 1, $methodName);

                    return $this->set($property, $args[0]);
                case 'is':
                case 'get':
                    $this->checkArguments($args, 0, 0, $methodName);

                    return $this->get($property);
                case 'default':
                    throw new \BadMethodCallException("Method $methodName is not exist");
            }
        }

        return null;
    }

    /**
     * @param $property string Key
     *
     * @return mixed
     */
    public function get($property)
    {
        if (isset($this->container[$property])) {
            return $this->container[$property];
        }

        return null;
    }

    /**
     * @param $property string Key
     * @param $value    string Value
     *
     * @return self
     */
    public function set($property, $value)
    {
        $this->container[$property] = $value;

        return $this;
    }

    /**
     * Check if args are valid or not.
     *
     * @param array  $args       List of arguments
     * @param int    $min        integer Minimum valid params
     * @param int    $max        Maximum valid params
     * @param string $methodName Method name
     */
    protected function checkArguments(array $args, $min, $max, $methodName)
    {
        $argc = count($args);
        if ($argc < $min || $argc > $max) {
            throw new \BadMethodCallException("Method $methodName is not exist");
        }
    }

    /**
     * @return array
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
