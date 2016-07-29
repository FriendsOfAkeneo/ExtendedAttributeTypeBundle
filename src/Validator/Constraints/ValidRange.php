<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that a range product value is valid.
 *
 * @author    Damien Carcel <damien.carcel@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
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
