<?php

namespace Tests\Elasticsearch;

use Akeneo\Test\IntegrationTestsBundle\Security\SystemUserAuthenticator;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\AppKernelTest;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionFilterTest extends KernelTestCase
{
    /** @var ContainerInterface */
    protected $container;

    /** @var ProductQueryBuilderFactoryInterface */
    private $pqbFactory;

    public function setUp()
    {
        static::bootKernel(['debug' => false]);

        $this->container = static::$kernel->getContainer();
        $authenticator = new SystemUserAuthenticator($this->container);
        $authenticator->createSystemUser();

        $this->pqbFactory = $this->container->get('pim_catalog.query.product_query_builder_factory');
        $this->resetDB();
        $this->loadData();
    }

    public function testTextCollectionValueContainsItem()
    {
        $this->createProduct('first_sku', ['foo', 'bar', 'baz']);
        $this->createProduct('second_sku', ['bar', 'test']);
        $this->createProduct('third_sku', ['http://foo/bar/baz', 'foo']);

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_images', Operators::CONTAINS, 'bar');
        $products = $pqb->execute();

        $this->assertCount(2, $products);
        foreach ($products as $product) {
            $this->assertContains($product->getIdentifier(), ['first_sku', 'second_sku']);
        }

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_images', Operators::CONTAINS, 'http://foo/bar/baz');
        $products = $pqb->execute();

        $this->assertCount(1, $products);
        $this->assertEquals('third_sku', $products->current()->getIdentifier());
    }

    public function testTextCollectionValueDoesNotContainItem()
    {
        $this->createProduct('first_sku', ['foo', 'bar', 'baz']);
        $this->createProduct('second_sku', ['http://foo/bar/baz', 'foo']);

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_images', Operators::DOES_NOT_CONTAIN, 'foo');
        $products = $pqb->execute();
        $this->assertCount(0, $products);

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_images', Operators::DOES_NOT_CONTAIN, 'http://foo/bar/baz');
        $products = $pqb->execute();
        $this->assertCount(1, $products);
        $product = $products->current();
        $this->assertEquals('first_sku', $product->getIdentifier());

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_images', Operators::DOES_NOT_CONTAIN, 'http://foo/bar');
        $products = $pqb->execute();
        $this->assertCount(2, $products);
    }

    public function testEmptyAndNotEmptyFilters()
    {
        $this->createProduct('empty_sku');
        $this->createProduct('not_empty_sku', ['foo']);

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_images', Operators::IS_EMPTY, null);
        $products = $pqb->execute();

        $this->assertCount(1, $products);
        /** @var ProductInterface $product */
        $product = $products->current();
        $this->assertEquals('empty_sku', $product->getIdentifier());

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_images', Operators::IS_NOT_EMPTY, null);
        $products = $pqb->execute();

        $this->assertCount(1, $products);
        /** @var ProductInterface $product */
        $product = $products->current();
        $this->assertEquals('not_empty_sku', $product->getIdentifier());
    }

    /**
     * @param array
     */
    protected function loadData()
    {
        $attributeGroupRepo = $this->container->get('pim_catalog.repository.attribute_group');
        $attributeGroup = $attributeGroupRepo->findOneByIdentifier('other');
        if (null === $attributeGroup) {
            $attributeGroup = $this->container->get('pim_catalog.factory.attribute_group')->create();
            $this->container->get('pim_catalog.updater.attribute_group')->update($attributeGroup, [
                'code' => 'other',
            ]);
            $this->container->get('pim_catalog.saver.attribute_group')->save($attributeGroup);
        }

        $attributeFactory = $this->container->get('pim_catalog.factory.attribute');
        $attributeUpdater = $this->container->get('pim_catalog.updater.attribute');
        $attributeSaver = $this->container->get('pim_catalog.saver.attribute');

        $attributeRepo = $this->container->get('pim_catalog.repository.attribute');
        $identifier = $attributeRepo->getIdentifier();
        if (null === $identifier) {
            $identifier = $attributeFactory->createAttribute(AttributeTypes::IDENTIFIER);
            $attributeUpdater->update(
                $identifier,
                [
                    'group' => 'other',
                    'code'  => 'sku',
                ]
            );
            $attributeSaver->save($identifier);
        }

        $textCollection = $attributeFactory->createAttribute(ExtendedAttributeTypes::TEXT_COLLECTION);
        $attributeUpdater->update(
            $textCollection,
            [
                'group' => 'other',
                'code'  => 'my_images',
            ]
        );
        $attributeSaver->save($textCollection);
    }

    /**
     * @param string $identifier
     * @param array $textCollection
     */
    protected function createProduct($identifier, $textCollection = [])
    {
        $product = $this->container->get('pim_catalog.builder.product')->createProduct($identifier);
        $productData = ['my_images' => [['data' => $textCollection, 'locale' => null, 'scope' => null]]];
        $this->container->get('pim_catalog.updater.product')->update($product, ['values' => $productData]);
        $this->container->get('pim_catalog.saver.product')->save($product);

        sleep(2);
    }

    protected function resetDB()
    {
        $productRepo = $this->container->get('pim_catalog.repository.product');
        $productRemover = $this->container->get('pim_catalog.remover.product');
        $products = $productRepo->findAll();
        $productRemover->removeAll($products);

        $attributeRepo = $this->container->get('pim_catalog.repository.attribute');
        $attributeRemover = $this->container->get('pim_catalog.remover.attribute');
        $textCollectionAttribute = $attributeRepo->findOneByIdentifier('my_images');
        if ($textCollectionAttribute instanceof AttributeInterface) {
            $attributeRemover->remove($textCollectionAttribute);
        }
    }
}
