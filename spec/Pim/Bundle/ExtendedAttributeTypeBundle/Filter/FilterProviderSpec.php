<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\Filter;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\Filter\FilterProvider;

class FilterProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(FilterProvider::class);
    }
}
