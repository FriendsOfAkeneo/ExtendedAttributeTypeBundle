<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product;

use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\ExtendedAttributeTypes;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\ColumnsMerger as BaseColumnsMerger;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class ColumnsMerger extends BaseColumnsMerger
{
    public function merge(array $row)
    {
        $resultRow = [];
        $collectedMetrics = [];
        $collectedPrices = [];
        $collectedRanges = [];

        foreach ($row as $fieldName => $fieldValue) {
            $attributeInfos = $this->fieldExtractor->extractColumnInfo($fieldName);
            if (null !== $attributeInfos) {
                $attribute = $attributeInfos['attribute'];
                if (AttributeTypes::BACKEND_TYPE_METRIC === $attribute->getBackendType()) {
                    $collectedMetrics = $this->collectMetricData($collectedMetrics, $attributeInfos, $fieldValue);
                } elseif (AttributeTypes::BACKEND_TYPE_PRICE === $attribute->getBackendType()) {
                    $collectedPrices = $this->collectPriceData($collectedPrices, $attributeInfos, $fieldValue);
                } elseif (ExtendedAttributeTypes::BACKEND_TYPE_RANGE === $attribute->getBackendType()) {
                    $collectedRanges = $this->collectRangeData($collectedRanges, $attributeInfos, $fieldValue);
                }
                else {
                    $resultRow[$fieldName] = $fieldValue;
                }
            } else {
                $resultRow[$fieldName] = $fieldValue;
            }
        }

        $resultRow = $this->mergeMetricData($resultRow, $collectedMetrics);
        $resultRow = $this->mergePriceData($resultRow, $collectedPrices);
        $resultRow = $this->mergeRangeData($resultRow, $collectedRanges);

        return $resultRow;
    }

    protected function collectRangeData(array $collectedRanges, array $attributeInfos, $fieldValue)
    {
        $cleanField = $this->getCleanFieldName($attributeInfos);

        if (null !== $attributeInfos['range_part']) {
            $collectedRanges[$cleanField][] = sprintf(
                '%s%s%s',
                $fieldValue,
                ' ',
                $attributeInfos['range_part']
            );
        }

        return $collectedRanges;
    }

    protected function mergeRangeData(array $resultRow, array $collectedRanges)
    {
        foreach ($collectedRanges as $fieldName => $ranges) {
            $resultRow[$fieldName] = implode(',', $ranges);
        }

        return $resultRow;
    }
}
