<?php

namespace MovingImage\Client\VMPro\Entity;

use MovingImage\Meta\Interfaces\EmbedCodeInterface;

class EmbedCode implements EmbedCodeInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
}
