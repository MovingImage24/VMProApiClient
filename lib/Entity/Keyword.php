<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;
use MovingImage\Meta\Interfaces\KeywordInterface;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getKeyword(): string
    {
        return $this->text;
    }

    public function setKeyword(string $keyword): self
    {
        $this->text = $keyword;

        return $this;
    }
}
