<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType;

use Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;

/**
 * Text collection attribute type
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionType extends AbstractAttributeType
{
    /** @var string */
    protected $backendType = AttributeTypes::BACKEND_TYPE_VARCHAR;

    /**
     * {@inheritdoc}
     */
    protected function defineCustomAttributeProperties(AttributeInterface $attribute)
    {
        $properties = parent::defineCustomAttributeProperties($attribute);

        $properties['unique']['options']['disabled'] = true;
        $properties['unique']['options']['read_only'] = true;

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return ExtendedAttributeTypes::TEXT_COLLECTION;
    }
}
