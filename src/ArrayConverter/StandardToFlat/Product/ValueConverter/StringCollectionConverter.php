<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\StandardToFlat\Product\ValueConverter;

use Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\AbstractValueConverter;
use Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\ValueConverterInterface;

/**
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class StringCollectionConverter extends AbstractValueConverter implements ValueConverterInterface
{
    /**
     * Converts a value
     *
     * @param string $attributeCode
     * @param mixed  $data
     *
     * @return array
     */
    public function convert($attributeCode, $data)
    {
        $convertedItem = [];

        foreach ($data as $value) {
            $flatName = $this->columnsResolver->resolveFlatAttributeName(
                $attributeCode,
                $value['locale'],
                $value['scope']
            );

            $arrayValues = $value['data'];
            $convertedItem[$flatName] = implode('|', $arrayValues);
        }

        return $convertedItem;
    }
}
