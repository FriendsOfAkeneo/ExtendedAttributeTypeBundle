<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

use Pim\Component\Catalog\Model\ProductValueInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
abstract class AbstractRange implements RangeInterface
{
    /** @var int */
    protected $id;

    /** @var double */
    protected $min;

    /** @var double */
    protected $max;

    /** @var ProductValueInterface */
    protected $value;

    /**
     * @return int
     */
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

    /**
     * {@inheritdoc}
     */
    public function setValue(ProductValueInterface $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (null !== $this->min || null !== $this->max)
            ? sprintf('from %s to %s', $this->min, $this->max)
            : '';
    }
}
