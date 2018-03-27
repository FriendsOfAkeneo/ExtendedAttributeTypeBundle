<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Elasticsearch\Filter\Attribute;

use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Pim\Component\Catalog\Exception\InvalidOperatorException;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Query\Filter\AttributeFilterInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use PimEnterprise\Bundle\WorkflowBundle\Elasticsearch\Filter\Attribute\AbstractAttributeFilter;
use PimEnterprise\Bundle\WorkflowBundle\Elasticsearch\Filter\Attribute\ProposalAttributePathResolver;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductProposalTextCollectionFilter extends AbstractAttributeFilter implements AttributeFilterInterface
{
    /**
     * @param ProposalAttributePathResolver $attributePathResolver
     * @param array $supportedAttributeTypes
     * @param array $supportedOperators
     */
    public function __construct(
        ProposalAttributePathResolver $attributePathResolver,
        array $supportedAttributeTypes = [],
        array $supportedOperators = []
    ) {
        $this->attributePathResolver = $attributePathResolver;
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

        if (Operators::IS_EMPTY !== $operator && Operators::IS_NOT_EMPTY !== $operator) {
            $this->checkValue($attribute, $value);
        }

        $attributePaths = $this->attributePathResolver->getAttributePaths($attribute, $locale, $channel);

        switch ($operator) {
            case Operators::CONTAINS:
                $clauses = $this->buildTermCondition($attributePaths, $value);
                $clause = $this->addBooleanClause($clauses);
                $this->searchQueryBuilder->addFilter($clause);
                break;

            case Operators::DOES_NOT_CONTAIN:
                $clauses = $this->buildTermCondition($attributePaths, $value);
                $mustNotClause = $this->addBooleanClause($clauses);

                $clauses = $this->buildExistsFieldCondition($attributePaths);
                $filterClause = $this->addBooleanClause($clauses);

                $this->searchQueryBuilder->addMustNot($mustNotClause);
                $this->searchQueryBuilder->addFilter($filterClause);
                break;

            case Operators::IS_EMPTY:
                $clauses = $this->buildExistsFieldCondition($attributePaths);
                $clause = $this->addBooleanClause($clauses);
                $this->searchQueryBuilder->addMustNot($clause);
                break;

            case Operators::IS_NOT_EMPTY:
                $clauses = $this->buildExistsFieldCondition($attributePaths);
                $clause = $this->addBooleanClause($clauses);
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
     * @param mixed $value
     */
    protected function checkValue(AttributeInterface $attribute, $value): void
    {
        if (!is_string($value) && null !== $value) {
            throw InvalidPropertyTypeException::stringExpected($attribute->getCode(), static::class, $value);
        }
    }

    /**
     * @param array $attributePaths
     * @param string $value
     *
     * @return array
     */
    private function buildTermCondition(array $attributePaths, string $value): array
    {
        return array_map(
            function ($attributePath) use ($value) {
                return [
                    'term' => [
                        $attributePath => $value,
                    ],
                ];
            },
            $attributePaths
        );
    }

    /**
     * @param array $attributePaths
     *
     * @return array
     */
    private function buildExistsFieldCondition(array $attributePaths): array
    {
        return array_map(
            function ($attributePath) {
                return [
                    'exists' => [
                        'field' => $attributePath,
                    ],
                ];
            },
            $attributePaths
        );
    }
}
