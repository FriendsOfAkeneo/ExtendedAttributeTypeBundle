<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Localization\Presenter;

use Akeneo\Component\Localization\Presenter\NumberPresenter;

/**
 * Range presenter, able to render range data localized and readable for a human.
 * Used to present read-only data: datagrid, versioning and published products.
 *
 * @author Damien Carcel <damien.carcel@akeneo.com>
 */
class RangePresenter extends NumberPresenter
{
    /**
     * {@inheritdoc}
     *
     * Values coming from versioning are not provided as an array, but as a
     * single value (on for the "min" field, another for the "max" field.
     */
    public function present($value, array $options = [])
    {
        if (!is_array($value)) {
            return parent::present($value, $options);
        }

        $min = isset($value['min']) ? parent::present($value['min'], $options) : null;
        $max = isset($value['max']) ? parent::present($value['max'], $options) : null;

        return trim(sprintf('Min: %s - Max: %s', $min, $max));
    }
}
