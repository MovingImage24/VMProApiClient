<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;
use MovingImage\Meta\Interfaces\VideoManagerInterface;

class VideoManager implements VideoManagerInterface
{
    /**
     * @Type("integer")
     */
    private $id;

    /**
     * @Type("string")
     */
    private $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
