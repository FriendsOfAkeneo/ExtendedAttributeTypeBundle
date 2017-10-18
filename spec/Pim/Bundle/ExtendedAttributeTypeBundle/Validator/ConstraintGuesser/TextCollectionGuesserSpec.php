<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\Validator\ConstraintGuesser;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;

class TextCollectionGuesserSpec extends ObjectBehavior
{
    function it_supports_text_collection(AttributeInterface $attribute)
    {
        $attribute->getType()->willReturn(ExtendedAttributeTypes::TEXT_COLLECTION);
        $this->supportAttribute($attribute)->shouldReturn(true);
    }
}
