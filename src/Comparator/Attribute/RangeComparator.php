<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Comparator\Attribute;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Component\Catalog\Comparator\ComparatorInterface;

/**
 * Comparator which computes change set for ranges, used in the product filter,
 * product association filter and the product draft builder.
 *
 * If one of the compared value is not a numeric, then the user entered a bad
 * value in the field. In this case, we return the data as it is so the
 * validation will return a violation and a message will be displayed in the PEF.
 *
 * @author Damien Carcel <damien.carcel@akeneo.com>
 */
class RangeComparator implements ComparatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($type)
    {
        return RangeType::TYPE_RANGE === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function compare($comparedValues, $originalValues)
    {
        $default = [
            'locale' => null,
            'scope' => null,
            'data' => ['min' => null, 'max' => null,]
        ];
        $originalValues = array_merge($default, $originalValues);

        if (!isset($comparedValues['data']['min'])
            && !isset($originalValues['data']['min'])
            && !isset($comparedValues['data']['max'])
            && !isset($originalValues['data']['max'])
        ) {
            return null;
        }

        if (!is_numeric($comparedValues['data']['min']) || !is_numeric($comparedValues['data']['max'])) {
            return $comparedValues;
        }

        $comparedValues['data']['min'] = (float) $comparedValues['data']['min'];
        $originalValues['data']['min'] = (float) $originalValues['data']['min'];
        $comparedValues['data']['max'] = (float) $comparedValues['data']['max'];
        $originalValues['data']['max'] = (float) $originalValues['data']['max'];

        $diff = array_diff_assoc((array) $comparedValues['data'], (array) $originalValues['data']);

        if (!empty($diff)) {
            return $comparedValues;
        }

        return null;
    }
}
