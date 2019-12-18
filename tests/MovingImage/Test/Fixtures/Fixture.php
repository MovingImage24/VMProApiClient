<?php

namespace MovingImage\Test\Fixtures;

class Fixture
{
    /**
     * Returns the API response for the specified method.
     *
     * @param string $method
     *
     * @return array
     */
    public static function getApiResponse($method)
    {
        return json_decode(self::getFixture('ApiResponse', $method.'.json'), true);
    }

    /**
     * @param string $type
     * @param string $name
     *
     * @return string|null
     */
    public static function getFixture($type, $name)
    {
        $fileName = __DIR__.'/Resources/'.$type.'/'.$name;
        if (file_exists($fileName)) {
            return file_get_contents($fileName) ?: null;
        }
    }
}
