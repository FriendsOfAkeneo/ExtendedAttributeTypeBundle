<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Job;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
use Akeneo\Component\Batch\Job\BatchStatus;
use Akeneo\Component\Batch\Model\JobInstance;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\AbstractTestCase;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportExportTest extends AbstractTestCase
{
    private $resourcePath = __DIR__ . '/../../resources';

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
                'family'     => 'first_family',
                'values'     => [
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
                'family'     => 'second_family',
                'values'     => [
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
                'family'     => 'second_family',
                'values'     => [
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

    public function testImportCreateProducts()
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
