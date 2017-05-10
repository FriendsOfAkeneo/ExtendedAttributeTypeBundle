<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle;

use Pim\Bundle\ElasticSearchBundle\Query\ProductQueryUtility;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimExtendedAttributeTypeBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        $registeredBundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists('PimElasticSearchBundle', $registeredBundles)) {
            ProductQueryUtility::addTypeSuffix(ExtendedAttributeTypes::TEXT_COLLECTION, ProductQueryUtility::SUFFIX_TEXT);
        }
    }
}
