<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\MongoDB\Normalizer\NormalizedData;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\RangeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Training\Bundle\AttributeTypeBundle\Model\AbstractRange;

/**
 * Normalizes product range to store it as mongodb_json.
 * This will be filled in the "normalizeData" field of the product document.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($range, $format = null, array $context = [])
    {
        $data = null;
        if (null !== $range->getMin() || null !== $range->getMax()) {
            $data = [
                'min' => '' !== $range->getMin() ? $range->getMin() : null,
                'max'   => '' !== $range->getMax() ? $range->getMax() : null,
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RangeInterface && 'mongodb_json' === $format;
    }
}
