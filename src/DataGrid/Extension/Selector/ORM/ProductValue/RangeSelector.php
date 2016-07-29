<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Extension\Selector\ORM\ProductValue;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datasource\DatasourceInterface;
use Pim\Bundle\DataGridBundle\Extension\Selector\SelectorInterface;

/**
 * Adds select part on the query builder for Range attribute type.
 * Used to select data in the product datagrid.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeSelector implements SelectorInterface
{
    /** @var SelectorInterface */
    protected $predecessor;

    /**
     * @param SelectorInterface $predecessor
     */
    public function __construct(SelectorInterface $predecessor)
    {
        $this->predecessor = $predecessor;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(DatasourceInterface $datasource, DatagridConfiguration $configuration)
    {
        $this->predecessor->apply($datasource, $configuration);

        $datasource
            ->getQueryBuilder()
            ->leftJoin('values.range', 'range')
            ->addSelect('range');
    }
}
