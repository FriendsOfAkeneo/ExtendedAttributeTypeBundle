<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Validator\ConstraintGuesser;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Validator\ConstraintGuesserInterface;
use Pim\Component\Catalog\Model\AttributeInterface;

/**
 * Validation guesser for the text collection attribute type.
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class TextCollectionGuesser implements ConstraintGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportAttribute(AttributeInterface $attribute)
    {
        return in_array(
            $attribute->getAttributeType(),
            [
                ExtendedAttributeTypes::TEXT_COLLECTION,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function guessConstraints(AttributeInterface $attribute)
    {
        return [];
    }
}
