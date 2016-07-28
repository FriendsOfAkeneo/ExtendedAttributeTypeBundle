<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Provider\EmptyValue;

use Pim\Bundle\EnrichBundle\Provider\EmptyValue\EmptyValueProviderInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Component\Catalog\Model\AttributeInterface;

/**
 * Empty value provider for range attributes.
 *
 * This is used in the attribute normalizer, during product normalization, if
 * product value is empty.
 *
 * @author Damien Carcel <damien.carcel@akeneo.com>
 */
class RangeEmptyValueProvider implements EmptyValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmptyValue($attribute)
    {
        return [
            'min' => null,
            'max'   => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        return $element instanceof AttributeInterface &&
            RangeType::TYPE_RANGE === $element->getAttributeType();
    }
}
