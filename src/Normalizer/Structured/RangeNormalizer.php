<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Normalizer\Structured;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\RangeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes Range attribute type to a structured format (json and xml).
 * Used to normalize data before displaying it in the PEF.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['json', 'xml'];

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'min' => $object->getFromData(),
            'max' => $object->getToData(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RangeInterface && in_array($format, $this->supportedFormats);
    }
}
