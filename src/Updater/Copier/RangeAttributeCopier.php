<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Updater\Copier;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\ProductRange;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Updater\Copier\AbstractAttributeCopier;

/**
 * Copies a range value in other range value.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeAttributeCopier extends AbstractAttributeCopier
{
    /** @var string[] */
    protected $supportedFromTypes = [ExtendedAttributeTypes::RANGE];

    /** @var string[] */
    protected $supportedToTypes = [ExtendedAttributeTypes::RANGE];

    /**
     * {@inheritdoc}
     */
    public function copyAttributeData(
        ProductInterface $fromProduct,
        ProductInterface $toProduct,
        AttributeInterface $fromAttribute,
        AttributeInterface $toAttribute,
        array $options = []
    ) {
        $options = $this->resolver->resolve($options);
        $fromLocale = $options['from_locale'];
        $toLocale   = $options['to_locale'];
        $fromScope  = $options['from_scope'];
        $toScope    = $options['to_scope'];

        $this->checkLocaleAndScope($fromAttribute, $fromLocale, $fromScope, 'range');
        $this->checkLocaleAndScope($toAttribute, $toLocale, $toScope, 'range');

        $this->copySingleValue(
            $fromProduct,
            $toProduct,
            $fromAttribute,
            $toAttribute,
            $fromLocale,
            $toLocale,
            $fromScope,
            $toScope
        );
    }

    /**
     * @param ProductInterface   $fromProduct
     * @param ProductInterface   $toProduct
     * @param AttributeInterface $fromAttribute
     * @param AttributeInterface $toAttribute
     * @param string             $fromLocale
     * @param string             $toLocale
     * @param string             $fromScope
     * @param string             $toScope
     */
    protected function copySingleValue(
        ProductInterface $fromProduct,
        ProductInterface $toProduct,
        AttributeInterface $fromAttribute,
        AttributeInterface $toAttribute,
        $fromLocale,
        $toLocale,
        $fromScope,
        $toScope
    ) {
        $fromValue = $fromProduct->getValue($fromAttribute->getCode(), $fromLocale, $fromScope);

        if (null !== $fromValue) {
            $min = $fromValue->getData();
            $toValue  = $toProduct->getValue($toAttribute->getCode(), $toLocale, $toScope);

            if (null === $toValue) {
                $toValue = $this->productBuilder->addProductValue($toProduct, $toAttribute, $toLocale, $toScope);
            }

            if (null === $range = $toValue->getRange()) {
                $range = new ProductRange();
            }

            $range->setMin($min->getMin());
            $range->setMax($min->getMax());

            $toValue->setRange($range);
        }
    }
}
