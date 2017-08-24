<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Filter\Doctrine\ORM;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\Filter\StringFilter;
use Pim\Component\Catalog\Query\Filter\Operators;

class TextCollectionFilter extends StringFilter
{
    /**
     * Escape slashes in URLs to match stored value.
     * The json_array doctrine type add backslashes before slashes and we therefor need
     * to add them in the LIKE filter condition.
     *
     * {@inheritdoc}
     */
    protected function prepareCondition($backendField, $operator, $value)
    {
        switch ($operator) {
            case Operators::STARTS_WITH:
            case Operators::ENDS_WITH:
            case Operators::CONTAINS:
            case Operators::DOES_NOT_CONTAIN:
            case Operators::EQUALS:
            case Operators::NOT_EQUAL:
                $value = addslashes(addcslashes($value, '/'));
                break;
        }

        return parent::prepareCondition($backendField, $operator, $value);
    }
}
