<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Completeness\Checker;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Completeness\Checker\ValueCompleteCheckerInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ValueInterface;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionCompleteChecker implements ValueCompleteCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isComplete(
        ValueInterface $value,
        ChannelInterface $channel = null,
        LocaleInterface $locale = null
    ) {
        if (null !== $value->getScope() && $channel !== $value->getScope()) {
            return false;
        }

        if (null !== $value->getLocale() && $locale !== $value->getLocale()) {
            return false;
        }

        $collection = $value->getData();

        return null !== $collection && count($collection) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsValue(
        ValueInterface $value,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        return ExtendedAttributeTypes::TEXT_COLLECTION === $value->getAttribute()->getType();
    }
}
