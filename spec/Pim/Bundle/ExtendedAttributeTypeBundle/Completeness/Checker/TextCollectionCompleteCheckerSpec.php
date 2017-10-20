<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\Completeness\Checker;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ValueInterface;

class TextCollectionCompleteCheckerSpec extends ObjectBehavior
{
    function it_check_supported_types(
        ValueInterface $value,
        AttributeInterface $attribute,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $value->getAttribute()->willReturn($attribute);
        $attribute->getType()->willReturn(ExtendedAttributeTypes::TEXT_COLLECTION);
        $this->supportsValue($value, $channel, $locale)->shouldReturn(true);

        $attribute->getType()->willReturn('any_other_type');
        $this->supportsValue($value, $channel, $locale)->shouldReturn(false);
    }

    function it_check_completeness(
        ValueInterface $value,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $value->getScope()->willReturn(null);
        $value->getLocale()->willReturn(null);
        $value->getData()->willReturn(['foo']);
        $this->isComplete($value, $channel, $locale)->shouldReturn(true);

        $value->getData()->willReturn([]);
        $this->isComplete($value, $channel, $locale)->shouldReturn(false);
    }
}
