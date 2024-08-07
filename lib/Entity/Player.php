<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Entity;

use JMS\Serializer\Annotation\Type;

class Player
{
    /** @Type("string") */
    private $id;

    /** @Type("string") */
    private $name;

    /** @Type("boolean") */
    private $active = false;

    private string $version = 'v1';

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }
}
