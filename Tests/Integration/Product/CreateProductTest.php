<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Product;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\AbstractTestCase;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Repository\ProductRepositoryInterface;
use Pim\Component\Catalog\Updater\ProductUpdater;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class CreateProductTest extends AbstractTestCase
{
    /** @var ContainerInterface */
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->container = static::$kernel->getContainer();

        $dataAttribute = ['code' => 'eans', 'type' => ExtendedAttributeTypes::TEXT_COLLECTION];
        $this->getDataLoader()->createAttribute($dataAttribute);

        $dataFamily = ['code' => 'tshirt', 'attributes' => ['eans']];
        $this->getDataLoader()->createFamily($dataFamily);

        $dataValues = ['eans' => [
            ['data' => ['foo'], 'scope' => null, 'locale' => null]]
        ];
        $dataProduct = ['family' => 'tshirt', 'values' => $dataValues];
        $this->getDataLoader()->createProduct('tshirt_red', $dataProduct);

        $this->clear();
    }

    public function testCreateProductWithTextCollection()
    {
        $dataValues = ['eans' => [
            ['data' => ['foo'], 'scope' => null, 'locale' => null]]
        ];
        $dataProduct = ['family' => 'tshirt', 'values' => $dataValues];
        $this->getDataLoader()->createProduct('tshirt_yellow', $dataProduct);
        $this->clear();

        $product = $this->getProductRepository()->findOneByIdentifier('tshirt_yellow');
        $this->assertInstanceOf(ProductInterface::class, $product);
        $this->assertEquals(['foo'], $product->getValue('eans')->getData());
    }

    public function testUpdateProductChangingItemOnTextCollection()
    {
        $product = $this->getProductRepository()->findOneByIdentifier('tshirt_red');
        $this->assertInstanceOf(ProductInterface::class, $product);
        $this->assertEquals(['foo'], $product->getValue('eans')->getData());

        // update product
        $dataValues = ['eans' => [
            ['data' => ['bar'], 'scope' => null, 'locale' => null]]
        ];
        $newData = ['family' => 'tshirt', 'values' => $dataValues];
        $this->getProductUpdater()->update($product, $newData);

        $violations = $this->validate($product);
        $this->assertCount(0, $this->validate($product));
        $this->assertEquals(['bar'], $product->getValue('eans')->getData());
        $this->saveProduct($product);

        // Check stored product
        $savedProduct = $this->getProductRepository()->findOneByIdentifier('tshirt_red');
        $this->assertInstanceOf(ProductInterface::class, $savedProduct);
        $this->assertEquals(['bar'], $savedProduct->getValue('eans')->getData());
    }

    public function testUpdateProductAddingItemOnTextCollection()
    {
        $product = $this->getProductRepository()->findOneByIdentifier('tshirt_red');
        $this->assertInstanceOf(ProductInterface::class, $product);
        $this->assertEquals(['foo'], $product->getValue('eans')->getData());

        // update product
        $dataValues = ['eans' => [
            ['data' => ['foo', 'bar'], 'scope' => null, 'locale' => null]]
        ];
        $newData = ['family' => 'tshirt', 'values' => $dataValues];
        $this->getProductUpdater()->update($product, $newData);

        $this->assertCount(0, $this->validate($product));
        $this->assertEquals(['foo', 'bar'], $product->getValue('eans')->getData());
        $this->saveProduct($product);

        // Check stored product
        $savedProduct = $this->getProductRepository()->findOneByIdentifier('tshirt_red');
        $this->assertInstanceOf(ProductInterface::class, $savedProduct);
        $this->assertEquals(['foo', 'bar'], $savedProduct->getValue('eans')->getData());
    }

    public function testUpdateProductDeletingItemOnTextCollection()
    {
        $product = $this->getProductRepository()->findOneByIdentifier('tshirt_red');
        $this->assertInstanceOf(ProductInterface::class, $product);
        $this->assertEquals(['foo'], $product->getValue('eans')->getData());

        // update product
        $dataValues = ['eans' => [
            ['data' => [], 'scope' => null, 'locale' => null]]
        ];
        $newData = ['family' => 'tshirt', 'values' => $dataValues];
        $this->getProductUpdater()->update($product, $newData);

        $this->assertCount(0, $this->validate($product));
        $this->assertEmpty($product->getValue('eans')->getData());
        $this->saveProduct($product);

        // Check stored product
        $savedProduct = $this->getProductRepository()->findOneByIdentifier('tshirt_red');
        $this->assertInstanceOf(ProductInterface::class, $savedProduct);
        $this->assertEmpty($savedProduct->getValue('eans')->getData());
    }

    /**
     * @return ProductUpdater
     */
    private function getProductUpdater()
    {
        return $this->get('pim_catalog.updater.product');
    }

    /**
     * @return ProductRepositoryInterface
     */
    private function getProductRepository()
    {
        return $this->get('pim_catalog.repository.product');
    }

    /**
     * @param ProductInterface $product
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    private function validate(ProductInterface $product)
    {
        return $this->container->get('pim_catalog.validator.product')->validate($product);
    }

    /**
     * @param ProductInterface $product
     */
    private function saveProduct(ProductInterface $product)
    {
        $this->container->get('pim_catalog.saver.product')->save($product);
        $this->clear();
    }
}
