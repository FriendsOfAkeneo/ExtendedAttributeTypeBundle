<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\Completeness\Checker;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ProductValueInterface;

class TextCollectionCompleteCheckerSpec extends ObjectBehavior
{
    function it_check_supported_types(ProductValueInterface $productValue, AttributeInterface $attribute)
    {
        $productValue->getAttribute()->willReturn($attribute);
        $attribute->getAttributeType()->willReturn(ExtendedAttributeTypes::TEXT_COLLECTION);
        $this->supportsValue($productValue)->shouldReturn(true);

        $attribute->getAttributeType()->willReturn('any_other_type');
        $this->supportsValue($productValue)->shouldReturn(false);
    }

    function it_check_completeness(
        ProductValueInterface $productValue,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $productValue->getData()->willReturn(['foo']);
        $this->isComplete($productValue, $channel, $locale)->shouldReturn(true);

        $productValue->getData()->willReturn([]);
        $this->isComplete($productValue, $channel, $locale)->shouldReturn(false);
    }
}
