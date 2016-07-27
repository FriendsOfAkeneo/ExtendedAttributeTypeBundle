<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
trait RangeTrait
{
    /** @var int */
    protected $id;

    /** @var double */
    protected $min;

    /** @var double */
    protected $max;

    /** @var ProductValueInterface */
    protected $value;

    public function getId()
    {
        return $this->id;
    }

    public function getMin()
    {
        return $this->min;
    }

    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function setMax($max)
    {
        $this->max = $max;
    }
}
