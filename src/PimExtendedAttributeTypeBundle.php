<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle;

use Pim\Bundle\ElasticSearchBundle\Query\ProductQueryUtility;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimExtendedAttributeTypeBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        if (class_exists('Pim\Bundle\ElasticSearchBundle\Query\ProductQueryUtility')) {
            ProductQueryUtility::addTypeSuffix('pim_catalog_text_collection', 'texts');
        }
    }
}
