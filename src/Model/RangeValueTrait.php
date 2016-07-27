<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
trait RangeValueTrait
{
    /** @var Range */
    protected $range;

    /**
     * @return Range
     */
    public function getRange()
    {
        if (is_object($this->range)) {
            $this->range->setValue($this);
        }

        return $this->range;
    }

    /**
     * @param Range $range
     *
     * @return ProductValue
     */
    public function setRange(RangeInterface $range)
    {
        $range->setValue($this);
        $this->range = $range;

        return $this;
    }
}
