<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Provider\Field;

use Pim\Bundle\EnrichBundle\Provider\Field\FieldProviderInterface;
use Pim\Component\Catalog\ExtendedAttributeTypes;

/**
 * Field provider for the Text collection attribute type.
 *
 * Used in the attribute normalizer, during product normalization.
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class TextCollectionProvider implements FieldProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getField($element)
    {
       return ExtendedAttributeTypes::TEXT_COLLECTION;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        return ExtendedAttributeTypes::TEXT_COLLECTION === $element->getAttributeType();
    }
}
