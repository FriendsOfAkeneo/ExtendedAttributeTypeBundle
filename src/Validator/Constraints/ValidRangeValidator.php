<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Validator\Constraints;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\RangeInterface;
use Pim\Component\Catalog\Model\ProductValueInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Checks that a range product value contains a Range object and validates it.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class ValidRangeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($object, Constraint $constraint)
    {
        if ($object instanceof ProductValueInterface) {
            $range = $object->getRange();

            if ($range instanceof RangeInterface) {
                $min = $range->getMin();
                $max = $range->getMax();

                if (is_numeric($min) && is_numeric($max) && $min > $max) {
                    $this->context->buildViolation($constraint->message)->addViolation();
                }
            }
        }
    }

    /**
     * Add the violations to the execution context.
     *
     * @param ConstraintViolationListInterface $violations
     */
    protected function addViolation(ConstraintViolationListInterface $violations)
    {
        foreach ($violations as $violation) {
            $this->context->buildViolation($violation->getMessage())->addViolation();
        }
    }
}
