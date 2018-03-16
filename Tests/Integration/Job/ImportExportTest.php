<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Job;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\AbstractTestCase;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportExportTest extends AbstractTestCase
{
    private $resourcePath = __DIR__ . '/../../resources';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadData();
        if (!file_exists('/tmp/integration')) {
            mkdir('/tmp/integration');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $finder = new Finder();
        $files = $finder->files()->name('*.csv')->in('/tmp/integration');

        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function testCsvExport()
    {
        $exportFilePath = '/tmp/integration/export_products.csv';

        $this->getDataLoader()->createJobInstance(
            'csv_product_export',
            'export',
            [
                'filePath' => $exportFilePath,
                'filters'  => [
                    'data'      => [],
                    'structure' => [
                        'scope'   => 'ecommerce',
                        'locales' => [
                            'en_US',
                            'es_ES',
                            'fr_FR',
                        ],
                    ],
                ],
            ]
        );

        $this->getDataLoader()->createProduct(
            'first_sku',
            [
                'family' => 'first_family',
                'values' => [
                    'name' => [
                        [
                            'locale' => null,
                            'scope'  => null,
                            'data'   => 'First product',
                        ],
                    ],
                ],
            ]
        );

        $this->getDataLoader()->createProduct(
            'second_sku',
            [
                'family' => 'second_family',
                'values' => [
                    'name'               => [
                        [
                            'locale' => null,
                            'scope'  => null,
                            'data'   => 'Second product',
                        ],
                    ],
                    'my_text_collection' => [
                        'en_US' => [
                            'locale' => 'en_US',
                            'scope'  => null,
                            'data'   => ['item 1', 'item 2'],
                        ],
                        'fr_FR' => [
                            'locale' => 'fr_FR',
                            'scope'  => null,
                            'data'   => ['test'],
                        ],
                        'es_ES' => [
                            'locale' => 'es_ES',
                            'scope'  => null,
                            'data'   => ['bar', 'baz', 'foo'],
                        ],
                    ],
                ],
            ]
        );

        $this->getDataLoader()->createProduct(
            'third_sku',
            [
                'family' => 'second_family',
                'values' => [
                    'name'               => [
                        [
                            'locale' => null,
                            'scope'  => null,
                            'data'   => 'Third product',
                        ],
                    ],
                    'my_text_collection' => [],
                ],
            ]
        );

        $status = $this->launch('csv_product_export');
        $this->assertSame(BatchCommand::EXIT_SUCCESS_CODE, $status);

        $this->assertFileExists($exportFilePath);
        $this->assertFileEquals($this->resourcePath . '/products.csv', $exportFilePath);
    }

    public function testImportCanCreateProducts()
    {
        $this->getDataLoader()->createJobInstance(
            'csv_product_import',
            'import',
            [
                'filePath' => $this->resourcePath . '/products.csv',
            ]
        );

        $status = $this->launch('csv_product_import');
        $this->assertSame(BatchCommand::EXIT_SUCCESS_CODE, $status);

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
                'filePath' => $this->resourcePath . '/products.csv',
            ]
        );

        $this->getDataLoader()->createProduct(
            'second_sku',
            [
                'family' => 'second_family',
                'values' => [
                    'name'               => [
                        [
                            'locale' => null,
                            'scope'  => null,
                            'data'   => 'Second product',
                        ],
                    ],
                    'my_text_collection' => [],
                ],
            ]
        );

        $this->getDataLoader()->createProduct(
            'third_sku',
            [
                'family' => 'second_family',
                'values' => [
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
            ]
        );

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

        $this->get('pim_catalog.validator.unique_value_set')->reset();
        $status = $this->launch('csv_product_import');
        $this->assertSame(BatchCommand::EXIT_SUCCESS_CODE, $status);

        $cursor = $pqb->execute();
        $secondSku = $cursor->current();
        $this->assertInstanceOf(ProductInterface::class, $secondSku);
        $this->assertContains('my_text_collection', $secondSku->getUsedAttributeCodes());
        $this->assertEquals(['item 1', 'item 2'], $secondSku->getvalue('my_text_collection', 'en_US')->getData());

        $cursor->next();
        $thirdSku = $cursor->current();
        $this->assertInstanceOf(ProductInterface::class, $thirdSku);
        $this->assertCount(2, $thirdSku->getValue('my_text_collection', 'en_US')->getData());
    }

    /**
     * @throws \Exception
     */
    private function loadData()
    {
        $this->getDataLoader()->activateLocales(['fr_FR', 'es_ES']);
        $this->getDataLoader()->createAttribute(
            [
                'code'        => 'my_text_collection',
                'type'        => ExtendedAttributeTypes::TEXT_COLLECTION,
                'localizable' => true,
            ]
        );
        $this->getDataLoader()->createAttribute(
            [
                'code' => 'name',
                'type' => AttributeTypes::TEXT,
            ]
        );

        $this->getDataLoader()->createFamily(
            [
                'code'       => 'first_family',
                'attributes' => [
                    'name',
                ],
            ]
        );

        $this->getDataLoader()->createFamily(
            [
                'code'       => 'second_family',
                'attributes' => [
                    'name',
                    'my_text_collection',
                ],
            ]
        );
    }
}
