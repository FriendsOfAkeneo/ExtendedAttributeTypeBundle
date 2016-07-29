<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Validator\ConstraintGuesser;

use Pim\Bundle\CatalogBundle\Validator\ConstraintGuesserInterface;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\RangeType;
use Pim\Bundle\ExtendedAttributeTypeBundle\Validator\Constraints\ValidRange;

/**
 * Validation guesser for the range attribute type.
 *
 * @author    Romain Monceau <romain@akeneo.com>
 */
class RangeGuesser implements ConstraintGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guessConstraints(AttributeInterface $attribute)
    {
        return [
            new ValidRange(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportAttribute(AttributeInterface $attribute)
    {
        return RangeType::TYPE_RANGE === $attribute->getAttributeType();
    }
}
