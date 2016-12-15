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
    protected $supportedTypes = [ExtendedAttributeTypes::RANGE];

    /**
     * {@inheritdoc}
     *
     * Expected data input format:
     * {
     *     "min": "12.0"|"12"|12|12.3,
     *     "max": "15.0"|"15"|15|15.3,
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
            $data = ['min' => null, 'max' => null];
        }

        $this->checkData($attribute, $data);

        $min = $data['min'];
        $max   = $data['max'];

        $this->setData($product, $attribute, $min, $max, $options['locale'], $options['scope']);
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
     * @param double             $min
     * @param double             $max
     * @param string             $locale
     * @param string             $scope
     */
    protected function setData(
        ProductInterface $product,
        AttributeInterface $attribute,
        $min,
        $max,
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

        $range->setMin($min);
        $range->setMax($max);

        $value->setRange($range);
    }
}
