<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType;

use Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType;
use Pim\Component\Catalog\Model\AttributeInterface;

/**
 * Text collection attribute type
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StringCollectionType extends AbstractAttributeType
{
    /**
     * {@inheritdoc}
     */
    protected function defineCustomAttributeProperties(AttributeInterface $attribute)
    {
        $properties = parent::defineCustomAttributeProperties($attribute) + [
                'validationRule' => [
                    'name'      => 'validationRule',
                    'fieldType' => 'choice',
                    'options'   => [
                        'choices' => [
                            null     => 'None',
                            'email'  => 'E-mail',
                            'url'    => 'URL',
                            'regexp' => 'Regular expression'
                        ],
                        'select2' => true
                    ]
                ],
                'validationRegexp' => [
                    'name' => 'validationRegexp'
                ]
            ];

        $properties['unique']['options']['disabled'] = false;
        $properties['unique']['options']['read_only'] = false;

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return ExtendedAttributeTypes::STRING_COLLECTION;
    }
}
