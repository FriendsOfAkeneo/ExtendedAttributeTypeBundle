<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType;

use Akeneo\Pim\Structure\Component\AttributeType\AbstractAttributeType;

/**
 * Text collection attribute type
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionType extends AbstractAttributeType
{
    /** @var string List separator for flat format */
    const FLAT_SEPARATOR = ',';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return ExtendedAttributeTypes::TEXT_COLLECTION;
    }
}
