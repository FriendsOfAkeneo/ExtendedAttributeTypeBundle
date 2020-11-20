<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Factory\Value;

//use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
//use Pim\Component\Catalog\AttributeTypes;
//use Pim\Component\Catalog\Factory\Value\ValueFactoryInterface;
//use Pim\Component\Catalog\Model\AttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Factory\Value\ScalarValueFactory;
use Akeneo\Pim\Enrichment\Component\Product\Factory\Value\ValueFactory;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;
//use Akeneo\Pim\Enrichment\Component\Product\Value\OptionsValue;
use Akeneo\Pim\Enrichment\Component\Product\Value\ScalarValue;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\Attribute;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Exception;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\TextCollectionType;
use Webmozart\Assert\Assert;

/**
 * Factory that creates simple product values (text, textarea and number).
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionValueFactory implements ValueFactory
{
    public function createWithoutCheckingData(Attribute $attribute, ?string $channelCode, ?string $localeCode, $data): ValueInterface
    {
        sort($data);
        $attributeCode = $attribute->code();

        if ($attribute->isLocalizableAndScopable()) {
            return ScalarValue::scopableLocalizableValue($attributeCode, $data, $channelCode, $localeCode);
        }

        if ($attribute->isScopable()) {
            return ScalarValue::scopableValue($attributeCode, $data, $channelCode);
        }

        if ($attribute->isLocalizable()) {
            return ScalarValue::localizableValue($attributeCode, $data, $localeCode);
        }

        return ScalarValue::value($attributeCode, $data);
    }

    public function createByCheckingData(Attribute $attribute, ?string $channelCode, ?string $localeCode, $data): ValueInterface
    {
        if (!is_array($data)) {
            throw InvalidPropertyTypeException::arrayExpected(
                $attribute->code(),
                static::class,
                $data
            );
        }

        try {
            Assert::allString($data);
        } catch (Exception $exception) {
            throw InvalidPropertyTypeException::validArrayStructureExpected(
                $attribute->code(),
                'one of the options is not a string',
                static::class,
                $data
            );
        }

        return $this->createWithoutCheckingData($attribute, $channelCode, $localeCode, $data);
    }

    public function supportedAttributeType(): string
    {
        return ExtendedAttributeTypes::TEXT_COLLECTION;
    }
//    /** @var string */
//    protected $productValueClass;
//
//    /** @var string */
//    protected $supportedAttributeTypes;
//x
//    /**
//     * @param string $productValueClass
//     * @param string $supportedAttributeTypes
//     */
//    public function __construct($productValueClass, $supportedAttributeTypes)
//    {
//        $this->productValueClass = $productValueClass;
//        $this->supportedAttributeTypes = $supportedAttributeTypes;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function create(AttributeInterface $attribute, $channelCode, $localeCode, $data)
//    {
//        $this->checkData($attribute, $data);
//
//        if (null !== $data) {
//            $data = $this->convertData($attribute, $data);
//        }
//
//        $value = new $this->productValueClass($attribute, $channelCode, $localeCode, $data);
//
//        return $value;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function supports($attributeType)
//    {
//        return $attributeType === $this->supportedAttributeTypes;
//    }
//
//    /**
//     * @param AttributeInterface $attribute
//     * @param mixed              $data
//     *
//     * @throws InvalidPropertyTypeException
//     */
//    protected function checkData(AttributeInterface $attribute, $data)
//    {
//        if (null === $data) {
//            return;
//        }
//
//        if (!is_array($data)) {
//            throw InvalidPropertyTypeException::arrayExpected(
//                $attribute->getCode(),
//                static::class,
//                $data
//            );
//        }
//    }
//
//    /**
//     * @param AttributeInterface $attribute
//     * @param mixed              $data
//     *
//     * @return mixed
//     */
//    protected function convertData(AttributeInterface $attribute, $data)
//    {
//        if (is_string($data) && '' === trim($data)) {
//            $data = null;
//        }
//
//        if (AttributeTypes::BOOLEAN === $attribute->getType() &&
//            (1 === $data || '1' === $data || 0 === $data || '0' === $data)
//        ) {
//            $data = boolval($data);
//        }
//
//        return $data;
//    }
}
