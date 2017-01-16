<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product;

use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\AttributeColumnInfoExtractor
    as BaseAttributeColumnInfoExtractor;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class AttributeColumnInfoExtractor extends BaseAttributeColumnInfoExtractor
{
    /**
     * {@inheritdoc}
     */
    protected function checkFieldNameTokens(AttributeInterface $attribute, $fieldName, array $explodedFieldName)
    {
        // the expected number of tokens in a field may vary,
        //  - with the current price import, the currency can be optionally present in the header,
        //  - with the current metric import, a "-unit" field can be added in the header,
        //
        // To avoid BC break, we keep the support in this fix, a next minor version could contain only the
        // support of currency code in the header and metric in a single field
        $isLocalizable = $attribute->isLocalizable();
        $isScopable = $attribute->isScopable();
        $isPrice = 'prices' === $attribute->getBackendType();
        $isMetric = 'metric' === $attribute->getBackendType();

        $expectedSize = 1;
        $expectedSize = $isLocalizable ? $expectedSize + 1 : $expectedSize;
        $expectedSize = $isScopable ? $expectedSize + 1 : $expectedSize;

        if ($isMetric || $isPrice) {
            $expectedSize = [$expectedSize, $expectedSize + 1];
        } else {
            $expectedSize = [$expectedSize];
        }

        $nbTokens = count($explodedFieldName);
        if (!in_array($nbTokens, $expectedSize)) {
            $expected = [
                $isLocalizable ? 'a locale' : 'no locale',
                $isScopable ? 'a scope' : 'no scope',
                $isPrice ? 'an optional currency' : 'no currency',
            ];
            $expected = implode($expected, ', ');

            throw new \InvalidArgumentException(
                sprintf(
                    'The field "%s" is not well-formatted, attribute "%s" expects %s',
                    $fieldName,
                    $attribute->getCode(),
                    $expected
                )
            );
        }
        if ($isLocalizable) {
            $this->checkForLocaleSpecificValue($attribute, $explodedFieldName);
        }
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
        }

        return $info;
    }
}
