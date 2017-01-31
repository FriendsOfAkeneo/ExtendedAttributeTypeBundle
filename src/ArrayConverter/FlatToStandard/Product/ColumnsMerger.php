<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product;

use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
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

        foreach ($row as $fieldName => $fieldValue) {
            $attributeInfos = $this->fieldExtractor->extractColumnInfo($fieldName);
            if (null !== $attributeInfos) {
                $attribute = $attributeInfos['attribute'];
                if (!$attribute instanceof AttributeInterface) {
                    throw new \RuntimeException('AttributeInterface expected.');
                }
                if (AttributeTypes::BACKEND_TYPE_METRIC === $attribute->getBackendType()) {
                    $collectedMetrics = $this->collectMetricData($collectedMetrics, $attributeInfos, $fieldValue);
                } elseif (AttributeTypes::BACKEND_TYPE_PRICE === $attribute->getBackendType()) {
                    $collectedPrices = $this->collectPriceData($collectedPrices, $attributeInfos, $fieldValue);
                } else {
                    $resultRow[$fieldName] = $fieldValue;
                }
            } else {
                $resultRow[$fieldName] = $fieldValue;
            }
        }

        $resultRow = $this->mergeMetricData($resultRow, $collectedMetrics);
        $resultRow = $this->mergePriceData($resultRow, $collectedPrices);

        return $resultRow;
    }
}
