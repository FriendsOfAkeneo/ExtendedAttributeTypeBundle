<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Filter\Doctrine\ORM;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\Filter\StringFilter;

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
        $value = addslashes(addcslashes($value, '/'));

        return parent::prepareCondition($backendField, $operator, $value);
    }
}
