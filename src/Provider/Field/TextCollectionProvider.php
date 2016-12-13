<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Provider\Field;

use Pim\Bundle\EnrichBundle\Provider\Field\FieldProviderInterface;

/**
 * CompositionProvider
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
       return 'pim_extended_attribute_text_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        return 'pim_extended_attribute_text_collection' === $element->getAttributeType();
    }
}
