<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Filter\ProductValue;

use Oro\Bundle\FilterBundle\Datasource\FilterDatasourceAdapterInterface;
use Oro\Bundle\FilterBundle\Filter\NumberFilter as OroNumberFilter;
use Oro\Bundle\FilterBundle\Form\Type\Filter\FilterType;
use Pim\Bundle\FilterBundle\Filter\ProductFilterUtility;
use Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Filter\Form\Type\RangeFilterType;

/**
 * Links the datagrid form type and the datagrid filter for Range attribute type.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeFilter extends OroNumberFilter
{
    /**
     * {@inheritdoc}
     */
    protected function getFormType()
    {
        return RangeFilterType::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(FilterDatasourceAdapterInterface $ds, $data)
    {
        $data = $this->parseData($data);
        if (!$data) {
            return false;
        }

        $operator = $this->getOperator($data['type']);
        $ds->generateParameterName($this->getName());

        $data['min'] = $data['value'];
        unset($data['value']);
        unset($data['type']);

        $this->util->applyFilter(
            $ds,
            $this->get(ProductFilterUtility::DATA_NAME_KEY),
            $operator,
            $data
        );

        return true;
    }

    /**
     * @param mixed $data
     *
     * @return array|bool
     */
    public function parseData($data)
    {
        if (!is_array($data) ||
            !array_key_exists('value', $data) ||
            !array_key_exists('max', $data)
        ) {
            return false;
        }

        $data['type'] = isset($data['type']) ? $data['type'] : null;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getOperator($type)
    {
        $operatorTypes = [
            FilterType::TYPE_EMPTY => 'EMPTY'
        ];

        return isset($operatorTypes[$type]) ? $operatorTypes[$type] : 'EMPTY';
    }
}
