<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

use Pim\Component\Catalog\Model\ProductValueInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
interface RangeInterface
{
    public function getId();

    public function getMin();

    public function setMin($min);

    public function getMax();

    public function setMax($max);
}
