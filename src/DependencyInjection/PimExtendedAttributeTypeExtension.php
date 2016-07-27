<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class PimExtendedAttributeTypeExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ .'/../Resources/config'));

        $this->loadStorageDriver($loader, $container);
    }

    /**
     * Load the mapping for product and product storage
     *
     * @param ContainerBuilder $container
     */
    protected function loadStorageDriver(LoaderInterface $loader, ContainerBuilder $container)
    {
        $storageDriver = $container->getParameter('pim_catalog_product_storage_driver');
        $storageConfig = sprintf('storage_driver/%s.yml', $storageDriver);

        if (file_exists(__DIR__ . '/../Resources/config/' . $storageConfig)) {
            $loader->load($storageConfig);
        }
    }
}
