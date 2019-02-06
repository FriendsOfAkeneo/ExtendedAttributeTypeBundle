<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Validator\ConstraintGuesser;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Validator\ChainedAttributeConstraintGuesser;
use Akeneo\Pim\Enrichment\Component\Product\Validator\ConstraintGuesserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Validation guesser for the text collection attribute type.
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
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
        return $attribute->getType() === ExtendedAttributeTypes::TEXT_COLLECTION;
    }

    /**
     * {@inheritdoc}
     */
    public function guessConstraints(AttributeInterface $attribute)
    {
        $guesser = new ChainedAttributeConstraintGuesser();

        if ('url' === $attribute->getValidationRule()) {
            $guesser->addConstraintGuesser(new UrlGuesser());
        } elseif ('regexp' === $attribute->getValidationRule() && $pattern = $attribute->getValidationRegexp()) {
            $guesser->addConstraintGuesser(new RegexGuesser());
        } elseif ('email' === $attribute->getValidationRule()) {
            $guesser->addConstraintGuesser(new EmailGuesser());
        }

        $guesser->addConstraintGuesser(new LengthGuesser());

        $constraints = $guesser->guessConstraints($attribute);
        $constraints[] = new Assert\NotBlank();

        return [
            new Assert\All(['constraints' => $constraints]),
        ];
    }
}
