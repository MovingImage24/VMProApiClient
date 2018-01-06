<?php

namespace MovingImage\Client\VMPro\Entity;

use MovingImage\Meta\Interfaces\KeywordInterface;

/**
 * Class Keyword
 */
class Keyword implements KeywordInterface
{
    /**
     * @Type("int")
     */
    private $id;

    /**
     * @Type("string")
     */
    private $text;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return Keyword
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyword()
    {
        return $this->text;
    }

    /**
     * @param $keyword
     *
     * @return Keyword
     */
    public function setKeyword($keyword)
    {
        $this->text = $keyword;

        return $this;
    }
}