<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\Filter\ProductValue;

use Oro\Bundle\FilterBundle\Filter\FilterUtility;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\Filter\ProductValue\TextCollectionFilter;
use Symfony\Component\Form\FormFactoryInterface;

class TextCollectionFilterSpec extends ObjectBehavior
{
    function it_is_initializable(FormFactoryInterface $factory, FilterUtility $util)
    {
        $this->beConstructedWith($factory, $util);
        $this->shouldBeAnInstanceOf(TextCollectionFilter::class);
    }
}
