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
     * single value (on for the "fromData" field, another for the "toData" field.
     */
    public function present($value, array $options = [])
    {
        if (!is_array($value)) {
            return parent::present($value, $options);
        }

        $fromData = isset($value['fromData']) ? parent::present($value['fromData'], $options) : null;
        $toData   = isset($value['toData']) ? parent::present($value['toData'], $options) : null;

        return trim(sprintf('From %s to %s', $fromData, $toData));
    }
}
