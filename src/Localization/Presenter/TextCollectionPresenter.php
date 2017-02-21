<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Localization\Presenter;

use Akeneo\Component\Localization\Presenter\PresenterInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;

/**
 * Text Collection presenter, able to render text collection data localized and readable for a human.
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionPresenter implements PresenterInterface
{
    /**
     * {@inheritdoc}
     */
    public function present($value, array $options = [])
    {
        if (null === $value) {
            return;
        }
        
        return sprintf('<ul><li>%s</li></ul>', implode('</li><li>', $value));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($attributeType)
    {
        return ExtendedAttributeTypes::TEXT_COLLECTION === $attributeType;
    }
}
