<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * DI for the ExtendedAttributeType bundle
 *
 * @author Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimExtendedAttributeTypeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ .'/../Resources/config'));
        $loader->load('array_converters.yml');
        $loader->load('attribute_types.yml');
        $loader->load('comparators.yml');
        $loader->load('completeness.yml');
        $loader->load('denormalizers.yml');
        $loader->load('form_types.yml');
        $loader->load('providers.yml');
        $loader->load('updaters.yml');
        $loader->load('validators.yml');

        $loader->load('entities.yml');
        $loader->load('factories.yml');
        $loader->load('query_builders.yml');
        if (class_exists('Akeneo\Pim\WorkOrganization\Workflow\Bundle\AkeneoPimWorkflowBundle')) {
            $loader->load('query_builders_ee.yml');
        }

        $loader->load('datagrid/attribute_types.yml');
        $loader->load('datagrid/filters.yml');
    }
}
