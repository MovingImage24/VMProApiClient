<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Util;

use BadMethodCallException;

trait AccessorTrait
{
    protected static $typeSnakeCase = 0;

    /**
     * @var int Set default type to snake case
     */
    private $type = 0;

    private $container = [];

    /**
     * @return mixed
     */
    public function __call(string $methodName, array $args)
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
                    $this->checkArguments($args, 0, 0, $methodName);

                    return $this->is($property);
                case 'get':
                    $this->checkArguments($args, 0, 0, $methodName);

                    return $this->get($property);
                case 'default':
                    throw new BadMethodCallException("Method $methodName is not exist");
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function get(string $property)
    {
        return $this->container[$property] ?? null;
    }

    public function is(string $property): ?bool
    {
        if (isset($this->container[$property])) {
            return 'true' === $this->container[$property];
        }

        return null;
    }

    public function set(string $property, $value): self
    {
        // we need to convert booleans into string, because these are query parameters
        if (is_bool($value)) {
            $value = $value
                ? 'true'
                : 'false';
        }

        $this->container[$property] = $value;

        return $this;
    }

    /**
     * Check if args are valid or not.
     */
    protected function checkArguments(array $args, int $min, int $max, string $methodName): void
    {
        $argc = count($args);
        if ($argc < $min || $argc > $max) {
            throw new BadMethodCallException("Method $methodName is not exist");
        }
    }

    public function getContainer(): array
    {
        return $this->container;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
