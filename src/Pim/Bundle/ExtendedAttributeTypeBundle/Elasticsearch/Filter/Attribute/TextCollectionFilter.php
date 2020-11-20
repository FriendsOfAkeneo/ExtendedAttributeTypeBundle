<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Elasticsearch\Filter\Attribute;

//use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
//use Pim\Bundle\CatalogBundle\Elasticsearch\Filter\Attribute\AbstractAttributeFilter;
//use Pim\Component\Catalog\Exception\InvalidOperatorException;
//use Pim\Component\Catalog\Model\AttributeInterface;
//use Pim\Component\Catalog\Query\Filter\AttributeFilterInterface;
//use Pim\Component\Catalog\Query\Filter\Operators;
//use Pim\Component\Catalog\Validator\AttributeValidatorHelper;
use Akeneo\Pim\Enrichment\Bundle\Elasticsearch\Filter\Attribute\AbstractAttributeFilter;
use Akeneo\Pim\Enrichment\Component\Product\Exception\InvalidOperatorException;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Pim\Enrichment\Component\Product\Validator\AttributeValidatorHelper;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Tool\Component\Elasticsearch\QueryString;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionFilter extends AbstractAttributeFilter
{
    /**
     * @param AttributeValidatorHelper $attrValidatorHelper
     * @param array                    $supportedAttributeTypes
     * @param array                    $supportedOperators
     */
    public function __construct(
        AttributeValidatorHelper $attrValidatorHelper,
        array $supportedAttributeTypes = [],
        array $supportedOperators = []
    ) {
        $this->attrValidatorHelper = $attrValidatorHelper;
        $this->supportedAttributeTypes = $supportedAttributeTypes;
        $this->supportedOperators = $supportedOperators;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributeFilter(
        AttributeInterface $attribute,
        $operator,
        $value,
        $locale = null,
        $channel = null,
        $options = []
    ) {
        if (null === $this->searchQueryBuilder) {
            throw new \LogicException('The search query builder is not initialized in the filter.');
        }

        $this->checkLocaleAndChannel($attribute, $locale, $channel);

        if (Operators::IS_EMPTY !== $operator && Operators::IS_NOT_EMPTY !== $operator) {
            $this->checkValue($attribute, $value);
        }

        $attributePath = $this->getAttributePath($attribute, $locale, $channel);

        /**
         * @todo probably have to change the generated queries?
         * @see \Akeneo\Pim\Enrichment\Bundle\Elasticsearch\Filter\Attribute\TextAreaFilter for examples
         */
        switch ($operator) {
            case Operators::CONTAINS:
                $clause = [
                    'wildcard' => [
                        $attributePath => '*' . $value . '*',
                    ],
//                    'default_field' => $attributePath,
//                    'query'         => '*' . QueryString::escapeValue($value) . '*',
                ];
                $this->searchQueryBuilder->addFilter($clause);
                break;

            case Operators::DOES_NOT_CONTAIN:
                $mustNotClause = [
                    'wildcard' => [
                        $attributePath => '*' . $value . '*',
                    ],
                ];
                $filterClause = [
                    'exists' => ['field' => $attributePath],
                ];

                $this->searchQueryBuilder->addMustNot($mustNotClause);
                $this->searchQueryBuilder->addFilter($filterClause);
                break;

            case Operators::IS_EMPTY:
                $clause = [
                    'exists' => [
                        'field' => $attributePath,
                    ],
                ];
                $this->searchQueryBuilder->addMustNot($clause);
                break;

            case Operators::IS_NOT_EMPTY:
                $clause = [
                    'exists' => [
                        'field' => $attributePath,
                    ],
                ];
                $this->searchQueryBuilder->addFilter($clause);
                break;

            default:
                throw InvalidOperatorException::notSupported($operator, static::class);
        }

        return $this;
    }

    /**
     * Check if the value is valid
     *
     * @param AttributeInterface $attribute
     * @param mixed              $value
     */
    protected function checkValue(AttributeInterface $attribute, $value)
    {
        if (!is_string($value) && null !== $value) {
            throw InvalidPropertyTypeException::stringExpected($attribute->getCode(), static::class, $value);
        }
    }
}
