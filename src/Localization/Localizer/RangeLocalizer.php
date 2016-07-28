<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Localization\Localizer;

use Akeneo\Component\Localization\Localizer\NumberLocalizer;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Checks and localizes/delocalizes if range provided fits the expected format.
 *
 * @author Damien Carcel <damien.carcel@akeneo.com>
 */
class RangeLocalizer extends NumberLocalizer
{
    /**
     * {@inheritdoc}
     */
    public function validate($range, $attributeCode, array $options = [])
    {
        $fromViolations = null;
        $toViolations   = null;

        if (isset($range['min'])) {
            $fromViolations = parent::validate($range['min'], $attributeCode, $options);
        }

        if (isset($range['max'])) {
            $toViolations = parent::validate($range['max'], $attributeCode, $options);
        }

        return $this->mergeViolations($fromViolations, $toViolations);
    }

    /**
     * {@inheritdoc}
     */
    public function delocalize($range, array $options = [])
    {
        if (isset($range['min'])) {
            if ('' === $range['min']) {
                $range['min'] = null;
            }
            $range['min'] = parent::delocalize($range['min'], $options);
        }

        if (isset($range['max'])) {
            if ('' === $range['max']) {
                $range['max'] = null;
            }
            $range['max'] = parent::delocalize($range['max'], $options);
        }

        return $range;
    }

    /**
     * {@inheritdoc}
     *
     * During product export, "$range" is not an array as fields are localized
     * individually.
     */
    public function localize($range, array $options = [])
    {
        if (!is_array($range)) {
            return parent::localize($range, $options);
        }

        if (isset($range['min'])) {
            $range['min'] = parent::localize($range['min'], $options);
        }
        if (isset($range['max'])) {
            $range['max'] = parent::localize($range['max'], $options);
        }

        return $range;
    }

    /**
     * @param ConstraintViolationListInterface $fromViolations
     * @param ConstraintViolationListInterface $toViolations
     *
     * @return null|ConstraintViolationListInterface
     */
    protected function mergeViolations(
        ConstraintViolationListInterface $fromViolations = null,
        ConstraintViolationListInterface $toViolations = null
    ) {
        if (null === $fromViolations && null === $toViolations) {
            return null;
        }

        $violations = new ConstraintViolationList();

        if (null !== $fromViolations) {
            foreach ($fromViolations as $violation) {
                $violations->add($violation);
            }
        }
        if (null !== $toViolations) {
            foreach ($toViolations as $violation) {
                $violations->add($violation);
            }
        }

        return $violations;
    }
}
