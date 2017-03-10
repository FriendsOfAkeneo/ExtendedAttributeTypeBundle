<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Extension\Formatter\Property\ProductValue;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Extension\Formatter\Property\ProductValue\TextCollectionProperty;
use Twig_Environment;

class TextCollectionPropertySpec extends ObjectBehavior
{
    function it_is_initializable(Twig_Environment $twig)
    {
        $this->beConstructedWith($twig);
        $this->shouldBeAnInstanceOf(TextCollectionProperty::class);
    }
}
