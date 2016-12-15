<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product\ValueConverter;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
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
 *      'temperature' => [{
 *          "locale": "fr_FR",
 *          "scope": null,
 *          "data": [
 *              {"min": 1500, "max": 200}
 *          ]
 *      }]
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

            $ranges = explode(',', $value);
            $data = [];

            foreach ($ranges as $range) {
                $rangeInfos = explode(' ', $range);
                $data[$rangeInfos[1]] = $rangeInfos[0];
            }


            return [
                $attributeFieldInfo['attribute']->getCode() => [[
                    'locale' => $attributeFieldInfo['locale_code'],
                    'scope'  => $attributeFieldInfo['scope_code'],
                    'data'   => $data
                ]]
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsField($attributeType)
    {
        return $attributeType === ExtendedAttributeTypes::RANGE;
    }
}
