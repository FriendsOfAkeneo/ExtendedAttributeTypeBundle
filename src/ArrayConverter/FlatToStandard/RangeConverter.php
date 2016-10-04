<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\ValueConverter\AbstractValueConverter;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeConverter extends AbstractValueConverter
{
    public function convert(array $attributeFieldInfo, $value)
    {
        if ('' === $value) {
            $value = null;
        } else {
            var_dump($value);
        }
    }

    public function supportsField($attributeType)
    {
        return $attributeType === RangeType::TYPE_RANGE;
    }
}
