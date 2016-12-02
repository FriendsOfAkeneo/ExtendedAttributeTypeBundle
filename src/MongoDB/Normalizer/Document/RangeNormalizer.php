<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\MongoDB\Normalizer\Document;

use Akeneo\Bundle\StorageUtilsBundle\MongoDB\MongoObjectsFactory;
use Pim\Bundle\CatalogBundle\MongoDB\Normalizer\Document\ProductNormalizer;
use Pim\Bundle\ExtendedAttributeTypeBundle\Model\RangeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes a range entity into an MongoDB Document.
 * This returns a MongoDB document, used to fill the "values" field of the
 * product document.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeNormalizer implements NormalizerInterface
{
    /** @var MongoObjectsFactory */
    protected $mongoFactory;

    /**
     * @param MongoObjectsFactory $mongoFactory
     */
    public function __construct(MongoObjectsFactory $mongoFactory)
    {
        $this->mongoFactory = $mongoFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof RangeInterface && ProductNormalizer::FORMAT === $format);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($range, $format = null, array $context = [])
    {
        $data = ['_id' => $this->mongoFactory->createMongoId()];

        $data['min'] = '' !== $range->getMin() ? $range->getMin() : null;
        $data['max']   = '' !== $range->getMax() ? $range->getMax() : null;

        return $data;
    }
}
