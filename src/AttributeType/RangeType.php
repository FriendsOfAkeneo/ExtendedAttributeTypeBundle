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
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return ExtendedAttributeTypes::RANGE;
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
