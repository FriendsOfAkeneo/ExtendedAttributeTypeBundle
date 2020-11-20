<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Normalizer\Indexing\Value;

//use Pim\Bundle\ExtendedAttributeTypeBundle\Model\TextCollectionValue;
//use Pim\Component\Catalog\Model\ValueInterface;
//use Pim\Component\Catalog\Normalizer\Indexing\Product\ProductNormalizer;
//use Pim\Component\Catalog\Normalizer\Indexing\ProductAndProductModel;
//use Pim\Component\Catalog\Normalizer\Indexing\ProductModel;
//use Pim\Component\Catalog\Normalizer\Indexing\Value\AbstractProductValueNormalizer;
//use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;
//use Akeneo\Pim\Enrichment\Component\Product\Normalizer\Indexing\ProductModel\ProductModelNormalizer;
use Akeneo\Pim\Enrichment\Component\Product\Normalizer\Indexing\Value\AbstractProductValueNormalizer;
use Akeneo\Pim\Enrichment\Component\Product\Normalizer\Indexing\Value\ValueCollectionNormalizer;
use Pim\Bundle\ExtendedAttributeTypeBundle\Model\TextCollectionValue;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;

/**
 * Normalizer for a options product value
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class TextCollectionNormalizer extends AbstractProductValueNormalizer implements CacheableSupportsMethodInterface
{
//    /**
//     * {@inheritdoc}
//     */
//    public function supportsNormalization($data, $format = null)
//    {
//        return $data instanceof TextCollectionValue && (
////                $format === PublishedProductNormalizer::INDEXING_FORMAT_PRODUCT_INDEX ||
//////                $format === ProductModel\ProductModelNormalizer::INDEXING_FORMAT_PRODUCT_MODEL_INDEX ||
////                $format === ValueCollectionNormalizer::INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX
////                $format === \Akeneo\Pim\Enrichment\Component\Product\Normalizer\Indexing\ProductAndProductModel\ProductModelNormalizer::INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX
//
//
//                $format === ProductNormalizer::INDEXING_FORMAT_PRODUCT_INDEX ||
//                $format === ProductModelNormalizer::INDEXING_FORMAT_PRODUCT_MODEL_INDEX ||
//                $format === \Akeneo\Pim\Enrichment\Component\Product\Normalizer\Indexing\ProductAndProductModel\ProductModelNormalizer::INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX
//            );
//    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof TextCollectionValue && (
                $format === ValueCollectionNormalizer::INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX
            );
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getNormalizedData(ValueInterface $value)
    {
        return $value->getData();
    }
}
