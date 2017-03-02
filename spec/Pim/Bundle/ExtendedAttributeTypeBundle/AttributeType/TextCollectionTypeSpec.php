<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductValueInterface;
use Pim\Component\Catalog\Validator\AttributeConstraintGuesser;
use Prophecy\Argument;
use Symfony\Component\Form\FormFactory;

class TextCollectionTypeSpec extends ObjectBehavior
{
    function let(AttributeConstraintGuesser $guesser, ProductValueInterface $value, AttributeInterface $name)
    {
        $value->getAttribute()->willReturn($name);

        $this->beConstructedWith(ExtendedAttributeTypes::BACKEND_TYPE_TEXT_COLLECTION, 'text', $guesser);
    }

    function it_builds_the_attribute_forms(FormFactory $factory, $name)
    {
        $name->getId()->willReturn(42);
        $name->getProperties()->willReturn([]);
        $name->setProperty(Argument::any(), Argument::any())->shouldBeCalled();
        $this->buildAttributeFormTypes($factory, $name)->shouldHaveCount(6);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('pim_catalog_text_collection');
    }
}
