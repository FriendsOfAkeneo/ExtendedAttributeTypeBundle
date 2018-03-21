<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Job;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
use Pim\Component\Catalog\Query\Filter\Operators;
use Symfony\Component\Finder\Finder;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ExportTest extends AbstractImportExportTestCase
{
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $finder = new Finder();
        $files = $finder->files()->name('*.csv')->in(static::$exportPath);

        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function testCsvExport()
    {
        $exportFilePath = static::$exportPath . '/export_products.csv';

        $this->getDataLoader()->createJobInstance(
            'csv_product_export',
            'export',
            [
                'filePath'   => $exportFilePath,
                'withHeader' => true,
                'filters'    => [
                    'data'      => [
                        [
                            'field'    => 'categories',
                            'operator' => Operators::IN_CHILDREN_LIST,
                            'value'    => ['master'],
                        ],
                        [
                            'field'    => 'completeness',
                            'operator' => 'ALL',
                            'value'    => 100,
                            'context'  => [
                                'locales' => [
                                    'en_US',
                                    'es_ES',
                                    'fr_FR',
                                ],
                            ],
                        ],
                    ],
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
                'categories' => ['master'],
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
                    'my_text_collection' => [],
                ],
                'categories' => ['master'],
            ]
        );

        $status = $this->launch('csv_product_export');
        $this->assertSame(BatchCommand::EXIT_SUCCESS_CODE, $status);

        $this->assertFileExists($exportFilePath);
        $this->assertCsvFileEqualsCsvFile(static::$resourcePath . '/products.csv', $exportFilePath);
    }
}
