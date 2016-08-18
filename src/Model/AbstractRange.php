<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

use Pim\Component\Catalog\Model\ProductValueInterface;

/**
 * Abstract class for range entity.
 * It is used by ProductRange and, in EE, by PublishedProductRange
 *
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * {@inheritdoc}
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * {@inheritdoc}
     */
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
