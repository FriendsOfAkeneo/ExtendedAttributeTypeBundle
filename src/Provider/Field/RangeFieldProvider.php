<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Provider\Field;

use Pim\Bundle\EnrichBundle\Provider\Field\FieldProviderInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;

/**
 * Field provider for the range attribute type.
 *
 * This is used in the attribute normalizer, during product normalization.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeFieldProvider implements FieldProviderInterface
{
    /** @var string[] */
    protected $fields = [
        ExtendedAttributeTypes::RANGE => 'pim-extended-attribute-type-range-field',
    ];

    /**
     * {@inheritdoc}
     */
    public function getField($attribute)
    {
        return $this->fields[$attribute->getAttributeType()];
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        return $element instanceof AttributeInterface && isset($this->fields[$element->getAttributeType()]);
    }
}
