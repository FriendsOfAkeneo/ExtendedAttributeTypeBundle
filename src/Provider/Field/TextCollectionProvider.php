<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Provider\Field;

use Pim\Bundle\EnrichBundle\Provider\Field\FieldProviderInterface;

/**
 * CompositionProvider
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class TextCollectionProvider implements FieldProviderInterface
{
    public function getField($element)
    {
       return 'pim_extended_attribute_text_collection';
    }

    /**
     * Does the Field provider support the element
     *
     * @param mixed $element
     *
     * @return bool
     */
    public function supports($element)
    {
        return 'pim_extended_attribute_text_collection' === $element->getAttributeType();
    }
}
