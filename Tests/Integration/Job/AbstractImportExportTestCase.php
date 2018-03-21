<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Job;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\AbstractTestCase;
use Pim\Component\Catalog\AttributeTypes;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AbstractImportExportTestCase extends AbstractTestCase
{
    /** @var string */
    protected static $exportPath = '/tmp/integration';

    /** @var string */
    protected static $resourcePath = __DIR__ . '/../../resources';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadData();
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

    /**
     * Checks that 2 csv files contain the same rows (no matter the order)
     *
     * @param string $expectedFilePath
     * @param string $actualFilePath
     * @param bool   $compareHeaders
     */
    protected function assertCsvFileEqualsCsvFile($expectedFilePath, $actualFilePath, $compareHeaders = true)
    {
        $expectedData = [];
        if (false === $expectedHandle = fopen($expectedFilePath, 'r')) {
            throw new \InvalidArgumentException(sprintf('File "%s" is not readable', $expectedFilePath));
        }
        while ($row = fgetcsv($expectedHandle, 0, ';')) {
            $expectedData[] = $row;
        }
        fclose($expectedHandle);

        if (false === $actualHandle = fopen($actualFilePath, 'r')) {
            throw new \InvalidArgumentException(sprintf('File "%s" is not readable', $actualFilePath));
        }
        if (true === $compareHeaders) {
            $actualHeader = fgetcsv($actualHandle, 0, ';');
            $this->assertEquals($expectedData[0], $actualHeader);
            unset($expectedData[0]);
        }
        while ($actualRow = fgetcsv($actualHandle, 0, ';')) {
            $this->assertContains($actualRow, $expectedData);
        }
        fclose($actualHandle);
    }
}
