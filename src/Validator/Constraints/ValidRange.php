<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that a range product value is valid.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class ValidRange extends Constraint
{
    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
