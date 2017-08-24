<?php

namespace spec\Pim\Bundle\ExtendedAttributeTypeBundle\Filter\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Query\Filter\AttributeFilterInterface;
use Pim\Component\Catalog\Validator\AttributeValidatorHelper;

class TextCollectionFilterSpec extends ObjectBehavior
{
    function let(QueryBuilder $queryBuilder, AttributeValidatorHelper $attrValidatorHelper)
    {
        $this->beConstructedWith(
            $attrValidatorHelper,
            ['pim_catalog_identifier'],
            ['STARTS WITH', 'ENDS WITH', 'CONTAINS', 'DOES NOT CONTAIN', 'EMPTY', 'NOT EMPTY']
        );
        $this->setQueryBuilder($queryBuilder);
    }

    function it_is_a_filter()
    {
        $this->shouldImplement(AttributeFilterInterface::class);
    }
}
