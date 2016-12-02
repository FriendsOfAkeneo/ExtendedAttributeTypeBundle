<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Normalizer\Structured;

use Akeneo\Component\Localization\Localizer\LocalizerInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\Model\ProductRange;
use Pim\Component\Catalog\Denormalizer\Structured\ProductValue\AbstractValueDenormalizer;

/**
 * Denormalizes Range attribute type from json format.
 * Used to create a range object and send it to the variant group form.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeDenormalizer extends AbstractValueDenormalizer
{
    /** @var LocalizerInterface */
    protected $localizer;

    /**
     * @param array              $supportedTypes
     * @param LocalizerInterface $localizer
     */
    public function __construct(array $supportedTypes, LocalizerInterface $localizer)
    {
        parent::__construct($supportedTypes);

        $this->localizer = $localizer;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (empty($data)) {
            return null;
        }

        $this->localizer->localize($data, $context);

        $range = new ProductRange();
        $range->setMin($data['min']);
        $range->setMax($data['max']);

        return $range;
    }
}
