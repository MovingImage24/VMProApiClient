<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\MetaDataSet;
use JMS\Serializer\Annotation\Type;

class MetaDataSetCollection
{
    /**
     * @var ArrayCollection<MetaDataSet>
     * @Type("ArrayCollection<MovingImage\Client\VMPro\Entity\MetaDataSet>")
     */
    private ArrayCollection $metaDataSets;

    /**
     * @param ArrayCollection<MetaDataSet> $metaDataSets
     */
    public function __construct(ArrayCollection $metaDataSets)
    {
        $this->metaDataSets = $metaDataSets;
    }

    /**
     * @return ArrayCollection<MetaDataSet>
     */
    public function getMetaDataSets(): ArrayCollection
    {
        return $this->metaDataSets;
    }
}
