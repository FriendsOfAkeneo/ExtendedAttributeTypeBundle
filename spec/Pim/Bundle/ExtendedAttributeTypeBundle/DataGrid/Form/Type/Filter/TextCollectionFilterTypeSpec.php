<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Form\Type\Filter;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Form\Type\Filter\TextCollectionFilterType;
use Symfony\Component\Translation\TranslatorInterface;

class TextCollectionFilterTypeSpec extends ObjectBehavior
{
    function it_is_initializable(TranslatorInterface $translator)
    {
        $this->beConstructedWith($translator);
        $this->shouldBeAnInstanceOf(TextCollectionFilterType::class);
    }
}
