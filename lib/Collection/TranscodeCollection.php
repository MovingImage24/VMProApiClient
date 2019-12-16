<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Collection;

use JMS\Serializer\Annotation as JMS;
use MovingImage\Meta\Interfaces\TranscodeInterface;

class TranscodeCollection
{
    /**
     * @var TranscodeInterface[]
     * @JMS\Type("array<MovingImage\Client\VMPro\Entity\Transcode>")
     */
    private $transcodes;

    /**
     * @param TranscodeInterface[] $transcodes
     */
    public function __construct(array $transcodes)
    {
        $this->transcodes = $transcodes;
    }

    /**
     * @return TranscodeInterface[]
     */
    public function getTranscodes(): array
    {
        return $this->transcodes;
    }

    /**
     * @param TranscodeInterface[] $transcodes
     */
    public function setTranscodes(array $transcodes): self
    {
        $this->transcodes = $transcodes;

        return $this;
    }
}
