<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\StandardToFlat\Product\ValueConverter;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\AttributeColumnsResolver;
use Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\AbstractValueConverter;
use Pim\Component\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\ValueConverterInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class TextCollectionConverter extends AbstractValueConverter implements ValueConverterInterface
{
    /** @var EncoderInterface */
    protected $encoder;

    /**
     * {@inheritdoc}
     *
     * @param EncoderInterface $encoder
     */
    public function __construct(
        AttributeColumnsResolver $columnsResolver,
        array $supportedAttributeTypes,
        EncoderInterface $encoder
    ) {
        parent::__construct($columnsResolver, $supportedAttributeTypes);

        $this->encoder = $encoder;
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
        return ExtendedAttributeTypes::TEXT_COLLECTION === $attribute->getAttributeType();
    }

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

            $arrayValues = json_decode($value['data']);
            $convertedItem[$flatName] = $this->encoder->encode($arrayValues, 'csv');
        }

        return $convertedItem;
    }
}
