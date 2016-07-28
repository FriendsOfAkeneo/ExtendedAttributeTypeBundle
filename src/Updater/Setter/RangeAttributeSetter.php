<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Updater\Setter;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\ProductRange;
use Pim\Component\Catalog\Exception\InvalidArgumentException;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Updater\Setter\AbstractAttributeSetter;

/**
 * Sets a range value in many products.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeAttributeSetter extends AbstractAttributeSetter
{
    /** @var string[] */
    protected $supportedTypes = ['pim_extended_attribute_type_range'];

    /**
     * {@inheritdoc}
     *
     * Expected data input format:
     * {
     *     "fromData": "12.0"|"12"|12|12.3,
     *     "toData": "15.0"|"15"|15|15.3,
     * }
     */
    public function setAttributeData(
        ProductInterface $product,
        AttributeInterface $attribute,
        $data,
        array $options = []
    ) {
        $options = $this->resolver->resolve($options);
        $this->checkLocaleAndScope($attribute, $options['locale'], $options['scope'], 'range');

        if (null === $data) {
            $data = ['fromData' => null, 'toData' => null];
        }

        $this->checkData($attribute, $data);

        $fromData = $data['fromData'];
        $toData   = $data['toData'];

        $this->setData($product, $attribute, $fromData, $toData, $options['locale'], $options['scope']);
    }

    /**
     * Checks if data is valid
     *
     * @param AttributeInterface $attribute
     * @param mixed              $data
     *
     * @throws InvalidArgumentException
     */
    protected function checkData(AttributeInterface $attribute, $data)
    {
        if (!is_array($data)) {
            throw InvalidArgumentException::arrayExpected($attribute->getCode(), 'setter', 'range', gettype($data));
        }

        if (!array_key_exists('min', $data)) {
            throw InvalidArgumentException::arrayKeyExpected(
                $attribute->getCode(),
                'min',
                'setter',
                'range',
                $this->invalidDataToString($data)
            );
        }

        if (!array_key_exists('max', $data)) {
            throw InvalidArgumentException::arrayKeyExpected(
                $attribute->getCode(),
                'max',
                'setter',
                'range',
                $this->invalidDataToString($data)
            );
        }
    }

    /**
     * Transforms the range data into a string if they are not valid.
     *
     * @param $data
     *
     * @return string
     */
    protected function invalidDataToString($data)
    {
        $invalidData = [];

        foreach ($data as $key => $value) {
            if (!is_scalar($value)) {
                $value = '...';
            }

            $invalidData[$key] = $value;
        }

        return print_r($invalidData, true);
    }

    /**
     * Sets the data into the product value
     *
     * @param ProductInterface   $product
     * @param AttributeInterface $attribute
     * @param double             $fromData
     * @param double             $toData
     * @param string             $locale
     * @param string             $scope
     */
    protected function setData(
        ProductInterface $product,
        AttributeInterface $attribute,
        $fromData,
        $toData,
        $locale,
        $scope
    ) {
        $value = $product->getValue($attribute->getCode(), $locale, $scope);

        if (null === $value) {
            $value = $this->productBuilder->addProductValue($product, $attribute, $locale, $scope);
        }

        if (null === $range = $value->getRange()) {
            $range = new ProductRange();
        }

        $range->setMin($fromData);
        $range->setMax($toData);

        $value->setRange($range);
    }
}
