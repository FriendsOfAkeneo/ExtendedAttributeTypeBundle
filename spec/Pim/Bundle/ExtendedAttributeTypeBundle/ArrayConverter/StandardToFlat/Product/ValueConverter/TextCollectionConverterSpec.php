<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\StandardToFlat\Product\ValueConverter;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\AttributeColumnsResolver;
use Prophecy\Argument;

/**
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class TextCollectionConverterSpec extends ObjectBehavior
{
    function let(AttributeColumnsResolver $columnsResolver)
    {
        $columnsResolver->resolveFlatAttributeName('my_collection', 'en_US', 'ecommerce')
            ->willReturn('my_collection-en_US-ecommerce');
        $this->beConstructedWith($columnsResolver, ['pim_catalog_text_collection']);
    }

    function it_is_a_converter()
    {
        $this->shouldImplement(
            'Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\ValueConverterInterface'
        );
    }

    function it_converts_text_collection_attribute_type()
    {
        $standard = [
            [
                'locale' => 'en_US',
                'scope'  => 'ecommerce',
                'data'   => [
                    'foo',
                    'bar',
                    'baz',
                ],
            ],
        ];

        $flat = [
            'my_collection-en_US-ecommerce' => 'foo,bar,baz',
        ];

        $this->convert('my_collection', $standard)->shouldReturn($flat);
    }

    function it_does_not_convert_anything_else()
    {
        $this->convert(Argument::not('my_collection'), Argument::Any())->shouldReturn([]);
    }
}
