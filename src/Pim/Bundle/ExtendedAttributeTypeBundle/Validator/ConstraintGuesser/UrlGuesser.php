<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Validator\ConstraintGuesser;

//use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
//use Pim\Component\Catalog\Model\AttributeInterface;
//use Pim\Component\Catalog\Validator\ConstraintGuesser\UrlGuesser as PimUrlGuesser;
use Akeneo\Pim\Enrichment\Component\Product\Validator\ConstraintGuesser\UrlGuesser as AkeneoUrlGuesser;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;

/**
 * Url guesser
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UrlGuesser extends AkeneoUrlGuesser
{
    /**
     * {@inheritdoc}
     */
    public function supportAttribute(AttributeInterface $attribute)
    {
        return ExtendedAttributeTypes::TEXT_COLLECTION === $attribute->getType();
    }
}
