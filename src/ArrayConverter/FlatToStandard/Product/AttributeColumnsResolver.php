<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product;

use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\ExtendedAttributeTypes;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\AttributeColumnsResolver
    as BaseAttributeColumnsResolver;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class AttributeColumnsResolver extends BaseAttributeColumnsResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolveAttributeField(array $value, array $currencyCodes)
    {
        $field = $this->resolveFlatAttributeName($value['attribute'], $value['locale'], $value['scope']);

        if (AttributeTypes::PRICE_COLLECTION === $value['type']) {
            $fields[] = $field;
            foreach ($currencyCodes as $currencyCode) {
                $currencyField = sprintf('%s-%s', $field, $currencyCode);
                $fields[] = $currencyField;
            }
        } elseif (AttributeTypes::METRIC === $value['type']) {
            $fields[] = $field;
            $metricField = sprintf('%s-%s', $field, 'unit');
            $fields[] = $metricField;
        } elseif (ExtendedAttributeTypes::RANGE === $value['type']) {
            $fields[] = sprintf('%s-min', $field);
            $fields[] = sprintf('%s-max', $field);
        } else {
            $fields[] = $field;
        }
    }
}
