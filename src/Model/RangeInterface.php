<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

use Pim\Component\Catalog\Model\ProductValueInterface;

/**
 * Interface for the range entity
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
interface RangeInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return double
     */
    public function getMin();

    /**
     * @param double $min
     *
     * @return RangeInterface
     */
    public function setMin($min);

    /**
     * @return double
     */
    public function getMax();

    /**
     * @param double $max
     *
     * @return RangeInterface
     */
    public function setMax($max);

    /**
     * @param ProductValueInterface $value
     *
     * @çeturn RangeInterface
     */
    public function setValue(ProductValueInterface $value);

    /**
     * @return ProductValueInterface
     */
    public function getValue();

    /**
     * @return string
     */
    public function __toString();
}
