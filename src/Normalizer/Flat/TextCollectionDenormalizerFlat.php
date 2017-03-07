<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Normalizer\Flat;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\TextCollectionType;
use Pim\Bundle\VersioningBundle\Denormalizer\Flat\ProductValue\AbstractValueDenormalizer;

/**
 * Denormalize flat text collection:
 *    before: $data = 'foo,bar,baz'
 *    after:  $data = ['foo', 'bar', 'baz']
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class TextCollectionDenormalizerFlat extends AbstractValueDenormalizer
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $data = ('' === $data) ? null : $data;

        if (null !== $data) {
            $data = explode(TextCollectionType::FLAT_SEPARATOR, $data);
        }

        return $data;
    }
}
