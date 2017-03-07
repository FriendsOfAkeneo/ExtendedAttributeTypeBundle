<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\Normalizer\Flat;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\TextCollectionType;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class TextCollectionDenormalizerFlatSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['pim_catalog_text_collection']);
    }

    function it_is_a_denormalizer()
    {
        $this->shouldBeAnInstanceOf(DenormalizerInterface::class);
    }

    function it_supports_text_collection_csv_denromalization()
    {
        $data = 'foo,bar,baz';
        $this->supportsDenormalization($data, 'pim_catalog_text_collection', 'csv')
            ->shouldReturn(true);
        $this->supportsDenormalization($data, 'pim_catalog_text_collection', 'other')
            ->shouldReturn(false);
        $this->supportsDenormalization($data, 'pim_catalog_other_type', 'csv')
            ->shouldReturn(false);
    }

    function it_denormalize_csv_text_collection()
    {
        $data = 'foo,bar,baz';
        $expected = ['foo','bar','baz'];

        $this->denormalize($data, TextCollectionType::class, 'csv')
            ->shouldReturn($expected);
    }
}
