<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\StandardToFlat\Product\ValueConverter;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\TextCollectionType;
use Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\AbstractValueConverter;
use Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\ValueConverterInterface;

/**
 * Standard to flat converter
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionConverter extends AbstractValueConverter implements ValueConverterInterface
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
            $convertedItem[$flatName] = implode(TextCollectionType::FLAT_SEPARATOR, $arrayValues);
        }

        return $convertedItem;
    }
}
