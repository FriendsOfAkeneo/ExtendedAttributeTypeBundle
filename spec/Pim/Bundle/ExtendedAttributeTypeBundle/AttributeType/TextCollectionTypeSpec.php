<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ValueInterface;
use Pim\Component\Catalog\Validator\AttributeConstraintGuesser;
use Prophecy\Argument;

class TextCollectionTypeSpec extends ObjectBehavior
{
    function let(AttributeConstraintGuesser $guesser, ValueInterface $value, AttributeInterface $name)
    {
        $value->getAttribute()->willReturn($name);

        $this->beConstructedWith(ExtendedAttributeTypes::BACKEND_TYPE_TEXT_COLLECTION, 'text', $guesser);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(ExtendedAttributeTypes::TEXT_COLLECTION);
    }
}
