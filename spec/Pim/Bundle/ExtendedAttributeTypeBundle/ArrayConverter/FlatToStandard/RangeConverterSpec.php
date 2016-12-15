<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
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
        $this->supportsField(ExtendedAttributeTypes::RANGE)->shouldReturn(true);
    }

    function it_does_not_convert_anything_else()
    {
        $this->supportsField(Argument::not(ExtendedAttributeTypes::RANGE))->shouldReturn(false);
    }
}
