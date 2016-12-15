<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Completeness\Checker;

use Pim\Component\Catalog\Completeness\Checker\ProductValueCompleteCheckerInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ProductValueInterface;

/**
 * Completeness checker for "range" attribute type.
 *
 * This class checks if a range product value is complete (contains a Range object
 * with both fields filled) or not.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeCompleteChecker implements ProductValueCompleteCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isComplete(
        ProductValueInterface $productValue,
        ChannelInterface $channel = null,
        LocaleInterface $locale = null
    ) {
        $range = $productValue->getRange();

        return null !== $range && null !== $range->getMin() && null !== $range->getMax();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsValue(ProductValueInterface $productValue)
    {
        return ExtendedAttributeTypes::RANGE === $productValue->getAttribute()->getAttributeType();
    }
}
