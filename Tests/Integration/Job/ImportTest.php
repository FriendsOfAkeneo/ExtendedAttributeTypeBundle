<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Job;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportTest extends AbstractImportExportTestCase
{
    public function testImportCanCreateProducts()
    {
        $this->getDataLoader()->createJobInstance(
            'csv_product_import',
            'import',
            [
                'filePath' => static::$resourcePath . '/products.csv',
            ]
        );

        $status = $this->launch('csv_product_import');
        $this->assertSame(BatchCommand::EXIT_SUCCESS_CODE, $status);

        $this->clear();

        /** @var ProductQueryBuilderInterface $pqb */
        $pqb = $this->get('pim_catalog.query.product_query_builder_factory')->create();
        $products = $pqb->execute();

        $this->assertCount(3, $products);
        $secondSku = $pqb->addFilter('sku', Operators::EQUALS, 'second_sku')->execute()->current();

        $this->assertInstanceOf(ProductInterface::class, $secondSku);
        $this->assertContains('my_text_collection', $secondSku->getUsedAttributeCodes());
        $this->assertEquals(['bar', 'baz', 'foo'], $secondSku->getValue('my_text_collection', 'es_ES')->getData());

        $pqb = $this->get('pim_catalog.query.product_query_builder_factory')->create();
        $thirdSku = $pqb->addFilter('sku', Operators::EQUALS, 'third_sku')->execute()->current();
        $this->assertInstanceOf(ProductInterface::class, $thirdSku);
        $this->assertNotContains('my_text_collection', $thirdSku->getUsedAttributeCodes());

        $myTextCollection = $this->get('pim_catalog.repository.attribute')->findOneByIdentifier('my_text_collection');
        $this->assertTrue($thirdSku->hasAttributeInFamily($myTextCollection));
    }

    public function testImportCanUpdateProducts()
    {
        $this->getDataLoader()->createJobInstance(
            'csv_product_import',
            'import',
            [
                'filePath' => static::$resourcePath . '/products.csv',
            ]
        );

        $this->getDataLoader()->createProduct(
            'second_sku',
            [
                'family'     => 'second_family',
                'values'     => [
                    'name'               => [
                        [
                            'locale' => null,
                            'scope'  => null,
                            'data'   => 'Second product',
                        ],
                    ],
                    'my_text_collection' => [],
                ],
                'categories' => ['master'],
            ]
        );

        $this->getDataLoader()->createProduct(
            'third_sku',
            [
                'family'     => 'second_family',
                'values'     => [
                    'name'               => [
                        [
                            'locale' => null,
                            'scope'  => null,
                            'data'   => 'Third product',
                        ],
                    ],
                    'my_text_collection' => [
                        'en_US' => [
                            'locale' => 'en_US',
                            'scope'  => null,
                            'data'   => ['ean_1', 'ean_2'],
                        ],
                    ],
                ],
                'categories' => ['master'],
            ]
        );

        $this->clear();
        $pqb = $this->get('pim_catalog.query.product_query_builder_factory')->create();
        $cursor = $pqb->addFilter('sku', Operators::IN_LIST, ['second_sku', 'third_sku'])->execute();

        $secondSku = $cursor->current();
        $this->assertInstanceOf(ProductInterface::class, $secondSku);
        $this->assertNotContains('my_text_collection', $secondSku->getUsedAttributeCodes());

        $cursor->next();
        $thirdSku = $cursor->current();
        $this->assertInstanceOf(ProductInterface::class, $thirdSku);
        $this->assertContains('my_text_collection', $thirdSku->getUsedAttributeCodes());
        $this->assertEquals(['ean_1', 'ean_2'], $thirdSku->getValue('my_text_collection', 'en_US')->getData());

        $this->clear();

        $status = $this->launch('csv_product_import');
        $this->assertSame(BatchCommand::EXIT_SUCCESS_CODE, $status);

        $this->clear();

        $pqb = $this->get('pim_catalog.query.product_query_builder_factory')->create();
        $cursor = $pqb->addFilter('sku', Operators::IN_LIST, ['second_sku'])->execute();
        $secondSku = $cursor->current();
        $this->assertInstanceOf(ProductInterface::class, $secondSku);
        $this->assertEquals('second_sku', $secondSku->getIdentifier());
        $this->assertContains('my_text_collection', $secondSku->getUsedAttributeCodes());
        $this->assertEquals(['item 1', 'item 2'], $secondSku->getValue('my_text_collection', 'en_US')->getData());

        $this->clear();
        $pqb = $this->get('pim_catalog.query.product_query_builder_factory')->create();
        $cursor = $pqb->addFilter('sku', Operators::IN_LIST, ['third_sku'])->execute();
        $thirdSku = $cursor->current();

        $this->assertInstanceOf(ProductInterface::class, $thirdSku);
        $this->assertCount(2, $thirdSku->getValue('my_text_collection', 'en_US')->getData());
    }
}
