<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\AttributeColumnInfoExtractor
    as BaseAttributeColumnInfoExtractor;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class AttributeColumnInfoExtractor extends BaseAttributeColumnInfoExtractor
{
    protected function calculateExpectedSize(AttributeInterface $attribute)
    {
        $isLocalizable = $attribute->isLocalizable();
        $isScopable = $attribute->isScopable();
        $isPrice = 'prices' === $attribute->getBackendType();
        $isMetric = 'metric' === $attribute->getBackendType();
        $isRange = 'range' === $attribute->getBackendType();

        $expectedSize = 1;
        $expectedSize = $isLocalizable ? $expectedSize + 1 : $expectedSize;
        $expectedSize = $isScopable ? $expectedSize + 1 : $expectedSize;

        if ($isMetric || $isPrice || $isRange) {
            $expectedSize = [$expectedSize, $expectedSize + 1];
        } else {
            $expectedSize = [$expectedSize];
        }

        return $expectedSize;
    }

    /**
     * {@inheritdoc}
     */
    protected function extractAttributeInfo(AttributeInterface $attribute, array $explodedFieldName)
    {
        array_shift($explodedFieldName);

        $info = [
            'attribute'   => $attribute,
            'locale_code' => $attribute->isLocalizable() ? array_shift($explodedFieldName) : null,
            'scope_code'  => $attribute->isScopable() ? array_shift($explodedFieldName) : null,
        ];

        if ('prices' === $attribute->getBackendType()) {
            $info['price_currency'] = array_shift($explodedFieldName);
        } elseif ('metric' === $attribute->getBackendType()) {
            $info['metric_unit'] = array_shift($explodedFieldName);
        } elseif (RangeType::TYPE_RANGE === $attribute->getAttributeType()) {
            $info['range_part'] = array_shift($explodedFieldName);
        }

        return $info;
    }
}
