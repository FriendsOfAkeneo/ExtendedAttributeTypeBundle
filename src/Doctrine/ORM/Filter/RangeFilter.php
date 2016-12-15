<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Doctrine\ORM\Filter;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\Filter\AbstractAttributeFilter;

use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Query\Filter\AttributeFilterInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Validator\AttributeValidatorHelper;

/**
 * Adds Range attribute type filter on the Product Query Builder.
 * Used by the product query builder in ORM.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeFilter extends AbstractAttributeFilter implements AttributeFilterInterface
{
    /**
     * @param AttributeValidatorHelper $attrValidatorHelper
     * @param array                    $supportedOperators
     */
    public function __construct(
        AttributeValidatorHelper $attrValidatorHelper,
        array $supportedOperators = []
    ) {
        $this->attrValidatorHelper = $attrValidatorHelper;
        $this->supportedOperators  = $supportedOperators;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributeFilter(
        AttributeInterface $attribute,
        $operator,
        $value,
        $locale = null,
        $scope = null,
        $options = []
    ) {
        $this->checkLocaleAndScope($attribute, $locale, $scope, 'range');

        if (Operators::IS_EMPTY === $operator) {
            $this->addEmptyFilter($attribute, $locale, $scope);
        } else {
            throw new \Exception(sprintf(
                'Operator %s not supported by range attribute filter',
                $operator
            ));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute(AttributeInterface $attribute)
    {
        return ExtendedAttributeTypes::RANGE === $attribute->getAttributeType();
    }

    /**
     * Adds a filter for an empty value
     *
     * @param AttributeInterface $attribute
     * @param string|null $locale
     * @param string|null $scope
     */
    protected function addEmptyFilter(AttributeInterface $attribute, $locale = null, $scope = null)
    {
        $backendType = $attribute->getBackendType();
        $joinAlias   = $this->getUniqueAlias('filter'.$attribute->getCode());

        // inner join to value
        $condition   = $this->prepareAttributeJoinCondition($attribute, $joinAlias, $locale, $scope);
        $rootAliases = $this->qb->getRootAliases();
        $this->qb->leftJoin(
            $rootAliases[0].'.values',
            $joinAlias,
            'WITH',
            $condition
        );

        $joinAliasOpt = $this->getUniqueAlias('filterR' . $attribute->getCode());
        $backendField = sprintf('%s.%s', $joinAliasOpt, 'min');

        $condition = $this->prepareCriteriaCondition($backendField, Operators::IS_EMPTY, null);
        $this->qb->leftJoin($joinAlias .'.'. $backendType, $joinAliasOpt);
        $this->qb->andWhere($condition);
    }
}
