<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType;

use Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType;
use Pim\Bundle\ExtendedAttributeTypeBundle\Model\ProductRange;
use Pim\Component\Catalog\Model\ProductValueInterface;

/**
 * Range attribute type
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeType extends AbstractAttributeType
{
    /** @const string */
    const TYPE_RANGE = 'pim_extended_attribute_type_range';

    /** @const string */
    const BACKEND_TYPE_RANGE = 'range';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return static::TYPE_RANGE;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValueFormData(ProductValueInterface $value)
    {
        if (null !== $value->getData()) {
            return $value->getData();
        }

        return new ProductRange();
    }
}
