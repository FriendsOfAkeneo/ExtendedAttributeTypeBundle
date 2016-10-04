<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\StandardToFlat;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\ValueConverterInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeConverter implements ValueConverterInterface
{

    /**
     * Does the converter supports the attribute
     *
     * @param AttributeInterface $attribute
     *
     * @return bool
     */
    public function supportsAttribute(AttributeInterface $attribute)
    {
        return RangeType::TYPE_RANGE === $attribute->getAttributeType();
    }

    /**
     * Converts a value
     *
     * @param string $attributeCode
     * @param mixed $data
     *
     * @return array
     */
    public function convert($attributeCode, $data)
    {
        // TODO: Implement convert() method.
    }
}
