<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Component\Catalog\AttributeTypes;
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
            $this->attributesFields[] = $field;
            foreach ($currencyCodes as $currencyCode) {
                $currencyField = sprintf('%s-%s', $field, $currencyCode);
                $this->attributesFields[] = $currencyField;
            }
        } elseif (AttributeTypes::METRIC === $value['type']) {
            $this->attributesFields[] = $field;
            $metricField = sprintf('%s-%s', $field, 'unit');
            $this->attributesFields[] = $metricField;
        } elseif (RangeType::TYPE_RANGE === $value['type']) {
            $this->attributesFields[] = sprintf('%s-min', $field);
            $this->attributesFields[] = sprintf('%s-max', $field);
        } else {
            $this->attributesFields[] = $field;
        }
    }
}
