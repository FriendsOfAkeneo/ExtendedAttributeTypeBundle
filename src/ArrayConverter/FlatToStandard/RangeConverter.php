<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\AttributeColumnInfoExtractor;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\ValueConverter\AbstractValueConverter;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\ValueConverter\ValueConverterInterface;

/**
 * Converts a range value from Akeneo PIM flat format to Akeneo PIM standard format
 *
 * Here is an example with the temperature attribute.
 * Flat format:
 * [
 *      'temperature-min' => 50,
 *      'temperature-max' => 75
 * ]
 *
 * Standard format:
 * [
 *      'temperature' => [
 *          'min' => 50,
 *          'max' => 75
 *      ]
 * ]
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeConverter implements ValueConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert(array $attributeFieldInfo, $value)
    {
        if ('' === $value) {
            $value = null;
        } else {

        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsField($attributeType)
    {
        return $attributeType === RangeType::TYPE_RANGE;
    }
}
