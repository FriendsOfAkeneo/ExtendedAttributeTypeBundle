<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

use Pim\Component\Catalog\Model\ProductValueInterface;

/**
 * Trait to reuse in the overridden ProductValue on the dedicated project
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
trait RangeValueTrait
{
    /** @var RangeInterface */
    protected $range;

    /**
     * @return RangeInterface
     */
    public function getRange()
    {
        if (is_object($this->range)) {
            $this->range->setValue($this);
        }

        return $this->range;
    }

    /**
     * @param RangeInterface $range
     *
     * @return ProductValueInterface
     */
    public function setRange(RangeInterface $range)
    {
        $range->setValue($this);
        $this->range = $range;

        return $this;
    }
}
