<?php

namespace MovingImage\Client\VMPro\Entity;

use MovingImage\Meta\Interfaces\EmbedCodeInterface;

class EmbedCode implements EmbedCodeInterface
{
    /**
     * @var string
     */
    private $code;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
