<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product\ValueConverter;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Prophecy\Argument;

/**
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class TextCollectionConverterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['pim_catalog_text_collection']);
    }

    function it_is_a_converter()
    {
        $this->shouldImplement(
            'Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\ValueConverter\ValueConverterInterface'
        );
    }

    function it_converts_text_collection_attribute_type()
    {
        $this->supportsField(ExtendedAttributeTypes::TEXT_COLLECTION)->shouldReturn(true);
    }

    function it_does_not_convert_anything_else()
    {
        $this->supportsField(Argument::not(ExtendedAttributeTypes::TEXT_COLLECTION))->shouldReturn(false);
    }
}
