<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Normalizer\Flat;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\ProductRange;
use Pim\Component\Catalog\Model\ProductValueInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $data = ('' === $data) ? null : $data;

        $resolver = new OptionsResolver();
        $this->configContext($resolver);
        $context = $resolver->resolve($context);

        return $this->setRangeData($context['value'], $data, $context['range_part']);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return ExtendedAttributeTypes::RANGE === $type && 'csv' === $format;
    }

    protected function setRangeData(ProductValueInterface $value, $data, $rangePart)
    {
        if (null === $range = $value->getRange()) {
            $range = new ProductRange();
        }

        if ('max' === $rangePart) {
            $range->setMax($data);
        } elseif ('min' === $rangePart) {
            $range->setMin($data);
        }

        return $range;
    }

    /**
     * Define context requirements
     *
     * @param OptionsResolver $resolver
     */
    protected function configContext(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['value', 'range_part'])
            ->setDefined(
                ['entity', 'locale_code', 'product', 'scope_code']
            );
    }
}
