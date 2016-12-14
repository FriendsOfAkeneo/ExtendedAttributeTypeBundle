<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Localization\Presenter;

use Akeneo\Component\Localization\Presenter\PresenterInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\TextCollectionType;

/**
 * Text collection presenter, able to render text collection data localized and readable for a human.
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
        $values = explode(';', $value);

        return sprintf('<ul><li>%s</li></ul>', implode('</li><li>', $values));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($attributeTypeCode)
    {
        return TextCollectionType::TYPE_TEXT_COLLECTION === $attributeTypeCode;
    }
}
