<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Doctrine\MongoDBODM\Filter;

use Pim\Bundle\CatalogBundle\Doctrine\MongoDBODM\Filter\AbstractAttributeFilter;
use Pim\Bundle\CatalogBundle\Doctrine\MongoDBODM\ProductQueryUtility;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Query\Filter\AttributeFilterInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Validator\AttributeValidatorHelper;

/**
 * Adds Range attribute type filter on the Product Query Builder.
 * Used by the product query builder in MongoDB.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeFilter extends AbstractAttributeFilter implements AttributeFilterInterface
{
    /** @var string */
    protected $supportedAttribute = ExtendedAttributeTypes::RANGE;

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
            $value = null;
        } else {
            throw new \Exception(sprintf(
                'Operator %s not supported by range attribute filter',
                $operator
            ));
        }

        $field = ProductQueryUtility::getNormalizedValueFieldFromAttribute($attribute, $locale, $scope);
        $field = sprintf('%s.%s', ProductQueryUtility::NORMALIZED_FIELD, $field);
        $fieldData = sprintf('%s.baseData', $field);

        $this->applyFilter($operator, $fieldData, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute(AttributeInterface $attribute)
    {
        return $attribute->getAttributeType() === $this->supportedAttribute;
    }

    /**
     * Allows to filter range data by applying a query with the given operator.
     *
     * @param string $operator
     * @param string $fieldData
     * @param float  $data
     *
     * @throws \Exception
     */
    protected function applyFilter($operator, $fieldData, $data)
    {
        switch ($operator) {
            case Operators::IS_EMPTY:
                $this->qb->field($fieldData)->equals(null);
                break;
            default:
                throw new \Exception(
                    sprintf(
                        'Operator %s not supported by range attribute filter',
                        $operator
                    )
                );
        }
    }
}
