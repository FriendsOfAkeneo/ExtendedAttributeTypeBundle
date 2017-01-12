<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\ArrayConverter\FlatToStandard\Product\ValueConverter;

use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\ValueConverter\ValueConverterInterface;

/**
 * Converts a range value from Akeneo PIM flat format to Akeneo PIM standard format
 *
 * Here is an example with the temperature attribute.
 * Flat format:
 * [
 *      'my-collection' => "foo|bar|baz",
 * ]
 *
 * Standard format:
 * [
 *      'my-collection' => [{
 *          "locale": "en_US",
 *          "scope": null,
 *          "data": [
 *              "foo",
 *              "bar",
 *              "baz",
 *          ]
 *      }]
 * ]
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class TextCollectionConverter implements ValueConverterInterface
{
    /** @var string[] */
    protected $supportedFieldTypes;

    /**
     * @param string[] $supportedFieldTypes
     */
    public function __construct(array $supportedFieldTypes)
    {
        $this->supportedFieldTypes = $supportedFieldTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsField($attributeType)
    {
        return in_array($attributeType, $this->supportedFieldTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $attributeFieldInfo, $value)
    {
        if ('' === trim($value)) {
            return null;
        }

        return [
            $attributeFieldInfo['attribute']->getCode() => [[
                'locale' => $attributeFieldInfo['locale_code'],
                'scope'  => $attributeFieldInfo['scope_code'],
                'data'   => explode('|', $value),
            ]],
        ];
    }
}
