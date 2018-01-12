<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\Elasticsearch\Filter\Attribute;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\Elasticsearch\Filter\Attribute\AbstractAttributeFilter;
use Pim\Bundle\CatalogBundle\Elasticsearch\SearchQueryBuilder;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Elasticsearch\Filter\Attribute\TextCollectionFilter;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Exception\InvalidOperatorException;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Query\Filter\AttributeFilterInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Validator\AttributeValidatorHelper;
use Prophecy\Argument;

class TextCollectionFilterSpec extends ObjectBehavior
{
    function let(
        AttributeValidatorHelper $attrValidatorHelper,
        AttributeInterface $urlList
    ) {
        $this->beConstructedWith(
            $attrValidatorHelper,
            [ExtendedAttributeTypes::TEXT_COLLECTION],
            [Operators::CONTAINS, Operators::DOES_NOT_CONTAIN, Operators::IS_EMPTY, Operators::IS_NOT_EMPTY]
        );

        $urlList->getType()->willReturn(ExtendedAttributeTypes::TEXT_COLLECTION);
        $urlList->getBackendType()->willReturn(ExtendedAttributeTypes::BACKEND_TYPE_TEXT_COLLECTION);
        $urlList->getCode()->willReturn('url_list');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TextCollectionFilter::class);
    }

    function it_is_a_filter()
    {
        $this->shouldImplement(AttributeFilterInterface::class);
        $this->shouldBeAnInstanceOf(AbstractAttributeFilter::class);
    }

    function it_supports_operators()
    {
        $this->getOperators()->shouldReturn(
            [
                'CONTAINS',
                'DOES NOT CONTAIN',
                'EMPTY',
                'NOT EMPTY',
            ]
        );
        $this->supportsOperator('CONTAINS')->shouldReturn(true);
        $this->supportsOperator('FAKE')->shouldReturn(false);
    }

    function it_supports_text_collection_attributes
    (
        AttributeInterface $urlList,
        AttributeInterface $textAttribute
    ) {
        $this->getAttributeTypes()->shouldReturn([ExtendedAttributeTypes::TEXT_COLLECTION]);

        $textAttribute->getType()->willReturn(AttributeTypes::TEXT);
        $this->supportsAttribute($textAttribute)->shouldReturn(false);
        $this->supportsAttribute($urlList)->shouldReturn(true);
    }

    function it_throws_an_exception_on_uninitialized_query_builder(AttributeInterface $urlList)
    {
        $this->shouldThrow(new \LogicException('The search query builder is not initialized in the filter.'))
             ->during(
                 'addAttributeFilter',
                 [
                     $urlList,
                     Argument::any(),
                     Argument::cetera(),
                 ]
             );
    }

    function it_throws_an_exception_on_unsupported_operator(
        SearchQueryBuilder $searchQueryBuilder,
        AttributeInterface $urlList
    ) {
        $this->setQueryBuilder($searchQueryBuilder);
        $operator = Operators::STARTS_WITH;
        $exception = InvalidOperatorException::notSupported($operator, TextCollectionFilter::class);
        $this->shouldThrow($exception)->during(
            'addAttributeFilter',
            [
                $urlList,
                $operator,
                'anystring',
            ]
        );
    }

    function it_adds_a_filter_with_operator_empty(
        $attrValidatorHelper,
        AttributeInterface $urlList,
        SearchQueryBuilder $sqb
    ) {
        $attrValidatorHelper->validateLocale($urlList, 'en_US')->shouldBeCalled();
        $attrValidatorHelper->validateScope($urlList, 'ecommerce')->shouldBeCalled();

        $sqb->addMustNot(
            [
                'exists' => [
                    'field' => 'values.url_list-textCollection.ecommerce.en_US',
                ],
            ]
        )->shouldBeCalled();

        $this->setQueryBuilder($sqb);
        $this->addAttributeFilter($urlList, Operators::IS_EMPTY, null, 'en_US', 'ecommerce', []);
    }

    function it_adds_a_filter_with_operator_is_not_empty(
        $attrValidatorHelper,
        AttributeInterface $urlList,
        SearchQueryBuilder $sqb
    ) {
        $attrValidatorHelper->validateLocale($urlList, 'en_US')->shouldBeCalled();
        $attrValidatorHelper->validateScope($urlList, 'ecommerce')->shouldBeCalled();

        $sqb->addFilter(
            [
                'exists' => [
                    'field' => 'values.url_list-textCollection.ecommerce.en_US',
                ],
            ]
        )->shouldBeCalled();

        $this->setQueryBuilder($sqb);
        $this->addAttributeFilter($urlList, Operators::IS_NOT_EMPTY, null, 'en_US', 'ecommerce', []);
    }

    function it_adds_a_filter_with_operator_contains(
        $attrValidatorHelper,
        AttributeInterface $urlList,
        SearchQueryBuilder $sqb
    ) {
        $attrValidatorHelper->validateLocale($urlList, 'en_US')->shouldBeCalled();
        $attrValidatorHelper->validateScope($urlList, 'ecommerce')->shouldBeCalled();

        $sqb->addFilter(
            [
                'term' => [
                    'values.url_list-textCollection.ecommerce.en_US' => 'http://fake-domain.null',
                ],
            ]
        )->shouldBeCalled();

        $this->setQueryBuilder($sqb);
        $this->addAttributeFilter($urlList, Operators::CONTAINS, 'http://fake-domain.null', 'en_US', 'ecommerce', []);
    }

    function it_adds_a_filter_with_operator_does_not_contain(
        $attrValidatorHelper,
        AttributeInterface $urlList,
        SearchQueryBuilder $sqb
    ) {
        $attrValidatorHelper->validateLocale($urlList, 'en_US')->shouldBeCalled();
        $attrValidatorHelper->validateScope($urlList, 'ecommerce')->shouldBeCalled();

        $sqb->addFilter([
                'exists' => [
                    'field' => 'values.url_list-textCollection.ecommerce.en_US',
                ],
            ]
        )->shouldBeCalled();

        $sqb->addMustNot(
            [
                'term' => [
                    'values.url_list-textCollection.ecommerce.en_US' => 'http://fake-domain.null',
                ],
            ]
        )->shouldBeCalled();

        $this->setQueryBuilder($sqb);
        $this->addAttributeFilter($urlList, Operators::DOES_NOT_CONTAIN, 'http://fake-domain.null', 'en_US', 'ecommerce', []);
    }
}
