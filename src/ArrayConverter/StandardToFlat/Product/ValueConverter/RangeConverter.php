<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\StandardToFlat\Product\ValueConverter;

use Pim\Component\Catalog\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\AttributeColumnsResolver;
use Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\ValueConverterInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeConverter implements ValueConverterInterface
{
    protected $columnResolver;

    /**
     * @param AttributeColumnsResolver $columnResolver
     */
    public function __construct(AttributeColumnsResolver $columnResolver)
    {
        $this->columnResolver = $columnResolver;
    }

    /**
     * Does the converter supports the attribute
     *
     * @param AttributeInterface $attribute
     *
     * @return bool
     */
    public function supportsAttribute(AttributeInterface $attribute)
    {
        return ExtendedAttributeTypes::RANGE === $attribute->getAttributeType();
    }

    /**
     * Converts a value
     *
     * @param string $attributeCode
     * @param mixed $data
     *
     * @return array
     */
    public function convert($attributeCode, $data)
    {
        $convertedItem = [];

        foreach ($data as $value) {
            $flatName = $this->columnResolver->resolveFlatAttributeName(
                $attributeCode,
                $value['locale'],
                $value['scope']
            );

            $convertedItem[sprintf('%s-min', $flatName)] = (string) $value['data']['min'];
            $convertedItem[sprintf('%s-max', $flatName)] = (string) $value['data']['max'];
        }

        return $convertedItem;
    }
}
