<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration;

use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class DataLoader
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $identifier
     * @param array  $data
     *
     * @return ProductInterface
     */
    public function createProduct($identifier, array $data)
    {
        $product = $this->container->get('pim_catalog.builder.product')->createProduct($identifier);
        $this->container->get('pim_catalog.updater.product')->update($product, $data);
        $constraintCollection = $this->container->get('pim_catalog.validator.product')->validate($product);
        if (count($constraintCollection) > 0) {
            throw new \Exception(sprintf('Product "%s" is not valid'), $product);
        }
        $this->container->get('pim_catalog.saver.product')->save($product);

        return $product;
    }

    /**
     * @param array $data
     *
     * @return AttributeInterface
     */
    public function createAttribute(array $data)
    {
        $attribute = $this->container->get('pim_catalog.factory.attribute')->createAttribute();
        $this->container->get('pim_catalog.updater.attribute')->update($attribute, $data);
        $constraintCollection = $this->container->get('validator')->validate($attribute);
        if (count($constraintCollection) > 0) {
            throw new \Exception(sprintf('Attribute "%s" is not valid'), $attribute);
        }
        $this->container->get('pim_catalog.saver.attribute')->save($attribute);

        return $attribute;
    }

    /**
     * @param array $data
     *
     * @return FamilyInterface
     */
    public function createFamily(array $data)
    {
        $family = $this->container->get('pim_catalog.factory.family')->create();
        $this->container->get('pim_catalog.updater.family')->update($family, $data);
        $constraintCollection = $this->container->get('validator')->validate($family);
        if (count($constraintCollection) > 0) {
            throw new \Exception(sprintf('Family "%s" is not valid'), $family);
        }
        $this->container->get('pim_catalog.saver.family')->save($family);

        return $family;
    }
}
