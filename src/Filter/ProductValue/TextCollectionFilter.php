<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Filter\ProductValue;

use Oro\Bundle\FilterBundle\Form\Type\Filter\FilterType;
use Oro\Bundle\FilterBundle\Form\Type\Filter\TextFilterType;
use Pim\Bundle\ExtendedAttributeTypeBundle\Form\Type\Filter\TextCollectionFilterType;
use Pim\Bundle\FilterBundle\Filter\ProductValue\StringFilter;
use Pim\Component\Catalog\Query\Filter\Operators;

class TextCollectionFilter extends StringFilter
{
    /** @var array */
    protected $operatorTypes = [
        TextFilterType::TYPE_CONTAINS     => Operators::CONTAINS,
        TextFilterType::TYPE_NOT_CONTAINS => Operators::DOES_NOT_CONTAIN,
        FilterType::TYPE_EMPTY            => Operators::IS_EMPTY,
    ];

    /**
     * {@inheritDoc}
     */
    protected function getFormType()
    {
        return TextCollectionFilterType::NAME;
    }
}
