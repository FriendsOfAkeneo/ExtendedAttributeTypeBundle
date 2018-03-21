<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Elasticsearch;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\AbstractTestCase;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionFilterTest extends AbstractTestCase
{
    /** @var ProductQueryBuilderFactoryInterface */
    private $pqbFactory;

    public function setUp()
    {
        parent::setUp();
        $this->loadData();
        $this->pqbFactory = $this->get('pim_catalog.query.product_query_builder_factory');
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

    protected function loadData()
    {
        $this->getDataLoader()->createAttribute(
            [
                'code' => 'my_images',
                'type' => ExtendedAttributeTypes::TEXT_COLLECTION,
            ]
        );
    }

    /**
     * @param string $identifier
     * @param array $textCollection
     */
    protected function createProduct($identifier, $textCollection = [])
    {
        $this->getDataLoader()->createProduct(
            $identifier,
            [
                'values'     => [
                    'my_images' => [
                        [
                            'data'   => $textCollection,
                            'locale' => null,
                            'scope'  => null,
                        ],
                    ],
                ],
                'categories' => ['master'],
            ]
        );
    }
}
