<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Provider\Field;

use Pim\Bundle\EnrichBundle\Provider\Field\FieldProviderInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;

class TextCollectionProvider implements FieldProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getField($element)
    {
        return ExtendedAttributeTypes::TEXT_COLLECTION;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        return $element instanceof AttributeInterface
            && ExtendedAttributeTypes::TEXT_COLLECTION === $element->getType();
    }
}
