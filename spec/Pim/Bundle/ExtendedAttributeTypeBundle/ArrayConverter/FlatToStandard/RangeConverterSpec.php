<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Component\Catalog\Model\AttributeInterface;
use Prophecy\Argument;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeConverterSpec extends ObjectBehavior
{
    function it_is_a_converter()
    {
        $this->shouldImplement(
            'Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\ValueConverter\ValueConverterInterface'
        );
    }

    function it_converts_range_attribute_type()
    {
        $this->supportsField(RangeType::TYPE_RANGE)->shouldReturn(true);
    }

    function it_does_not_convert_anything_else()
    {
        $this->supportsField(Argument::not(RangeType::TYPE_RANGE))->shouldReturn(false);
    }
}
