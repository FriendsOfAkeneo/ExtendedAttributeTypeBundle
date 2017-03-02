<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Completeness\Checker;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Completeness\Checker\ProductValueCompleteCheckerInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ProductValueInterface;

class TextCollectionCompleteChecker implements ProductValueCompleteCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isComplete(
        ProductValueInterface $productValue,
        ChannelInterface $channel = null,
        LocaleInterface $locale = null
    ) {
        $collection = $productValue->getData();

        return null !== $collection && count($collection) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsValue(ProductValueInterface $productValue)
    {
        return ExtendedAttributeTypes::TEXT_COLLECTION === $productValue->getAttribute()->getAttributeType();
    }
}
