<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Product;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\AbstractTestCase;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Query\Filter\Operators;

/**
 * @author    Julian PRUD'HOMME <julian.prudhomme@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CompletenessTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->getDataLoader()->createChannel(
            [
                'code'          => 'mobile',
                'labels'        => [
                    'en_US' => 'Mobile',
                    'fr_FR' => 'Mobile',
                ],
                'locales'       => ['en_US', 'fr_FR'],
                'currencies'    => ['USD', 'EUR'],
                'category_tree' => 'master',
            ]
        );
    }

    /**
     * @dataProvider providerTestWithOneChannelAndOneLocale
     */
    public function testWithOneChannelAndOneLocale(
        $productId,
        $productName,
        $family,
        $textCollection,
        $expectedCompleteness
    ) {
        $this->loadDataForSingleChannel();
        $this->createSimpleProduct($productId, $productName, $family, $textCollection);
        $this->assertEquals(
            $expectedCompleteness,
            $this->getCompletenessByChannelAndLocale($productId, 'en_US', 'ecommerce')
        );
    }

    public function providerTestWithOneChannelAndOneLocale()
    {
        return [
            'No attribute required'               => ['P1', 'P1', 'family_one', [], 100],
            '2 attributes required, one missing'  => ['P2', 'P2', 'family_two', [], 66],
            '2 attributes required, none missing' => ['P3', 'P3', 'family_two', ['ean_1', 'ean_2'], 100],
            'Only identifier is defined'          => ['P4', '', 'family_two', [], 33],
        ];
    }

    public function testWithMultipleChannels()
    {
        $this->getDataLoader()->activateLocales(['fr_FR']);

        $this->loadDataForMultipleChannels();

        $this->createComplexProduct('P1', 'englishP1', '', 'family_one', 'ecommerce');
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P1', 'en_US', 'ecommerce'));
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P1', 'fr_FR', 'ecommerce'));

        $this->createComplexProduct('P2', 'englishP2', 'frenchP2', 'family_one', 'ecommerce');
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P2', 'en_US', 'ecommerce'));
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P2', 'fr_FR', 'ecommerce'));

        $this->createComplexProduct('P3', 'englishP3', '', 'family_two', 'ecommerce');
        $this->assertEquals(66, $this->getCompletenessByChannelAndLocale('P3', 'en_US', 'ecommerce'));
        $this->assertEquals(33, $this->getCompletenessByChannelAndLocale('P3', 'fr_FR', 'ecommerce'));

        $this->createComplexProduct('P4', 'englishP4', 'frenchP4', 'family_two', 'ecommerce');
        $this->assertEquals(66, $this->getCompletenessByChannelAndLocale('P4', 'en_US', 'ecommerce'));
        $this->assertEquals(66, $this->getCompletenessByChannelAndLocale('P4', 'fr_FR', 'ecommerce'));

        $this->createComplexProduct('P5', 'englishP5', 'frenchP5', 'family_two', 'ecommerce', ['ean_1']);
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P5', 'en_US', 'ecommerce'));
        $this->assertEquals(66, $this->getCompletenessByChannelAndLocale('P5', 'fr_FR', 'ecommerce'));

        $this->createComplexProduct('P6', 'englishP6', 'frenchP6', 'family_two', 'ecommerce', ['ean_en'], ['ean_fr']);
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P6', 'en_US', 'ecommerce'));
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P6', 'fr_FR', 'ecommerce'));

        $this->createComplexProduct('P7', 'englishP7', 'frenchP7', 'family_two', 'mobile', ['ean_en'], ['ean_fr']);
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P7', 'en_US', 'mobile'));
        $this->assertEquals(100, $this->getCompletenessByChannelAndLocale('P7', 'fr_FR', 'mobile'));
        $this->assertEquals(33, $this->getCompletenessByChannelAndLocale('P7', 'en_US', 'ecommerce'));
        $this->assertEquals(33, $this->getCompletenessByChannelAndLocale('P7', 'fr_FR', 'ecommerce'));
    }

    private function getCompletenessByChannelAndLocale($productId, $locale, $channel)
    {
        $this->clear();
        $pqb = $this->get('pim_catalog.query.product_query_builder_factory')->create();
        $pqb->addFilter('sku', Operators::EQUALS, $productId);
        $sku = $pqb->execute()->current();

        foreach ($sku->getCompletenesses() as $completeness) {
            if ($completeness->getLocale()->getCode() == $locale && $completeness->getChannel()->getCode(
                ) == $channel) {
                return $completeness->getRatio();
            }
        }

        throw new \Exception(sprintf('Cannot get completeness for product %s', $productId));
    }

    private function loadDataForSingleChannel()
    {
        $this->getDataLoader()->createAttribute(
            [
                'code'        => 'name',
                'type'        => AttributeTypes::TEXT,
                'localizable' => false,
                'scopable'    => false,
            ]
        );

        $this->getDataLoader()->createAttribute(
            [
                'code'        => 'my_text_collection',
                'type'        => ExtendedAttributeTypes::TEXT_COLLECTION,
                'localizable' => false,
                'scopable'    => false,
            ]
        );

        $this->createFamilies();
    }

    private function loadDataForMultipleChannels()
    {
        $this->getDataLoader()->createAttribute(
            [
                'code'        => 'name',
                'type'        => AttributeTypes::TEXT,
                'localizable' => true,
                'scopable'    => true,
                'labels'      => [
                    'en_US' => 'name',
                    'fr_FR' => 'nom',
                ],
            ]
        );

        $this->getDataLoader()->createAttribute(
            [
                'code'        => 'my_text_collection',
                'type'        => ExtendedAttributeTypes::TEXT_COLLECTION,
                'localizable' => true,
                'scopable'    => true,
                'labels'      => [
                    'en_US' => 'text collection',
                    'fr_FR' => 'Collection de texte',
                ],
            ]
        );

        $this->createFamilies();
    }

    private function createSimpleProduct($id, $name, $family, array $textCollectionValues = [])
    {
        $data = [
            'family' => $family,
            'values' => [],
        ];

        $data['values']['name'] = [
                [
                    'locale' => null,
                    'scope'  => null,
                    'data'   => $name,
                ],
            ];

        $data['values']['my_text_collection'] = [
                [
                    'locale' => null,
                    'scope'  => null,
                    'data'   => $textCollectionValues,
                ],
            ];

        $this->getDataLoader()->createProduct($id, $data);
    }

    private function createComplexProduct(
        $identifier,
        $englishName,
        $frenchName,
        $family,
        $scope,
        array $englishTextCollectionValues = [],
        array $frenchTextCollectionValues = []
    ) {
        $data = [
            'family' => $family,
            'values' => [
                'name' => [
                    [
                        'locale' => 'en_US',
                        'scope' => $scope,
                        'data' => $englishName,
                    ],
                    [
                        'locale' => 'fr_FR',
                        'scope'  => $scope,
                        'data'   => $frenchName,
                    ],
                ],
                'my_text_collection' => [
                    [
                        'locale' => 'en_US',
                        'scope'  => $scope,
                        'data'   => $englishTextCollectionValues,
                    ],
                    [
                        'locale' => 'fr_FR',
                        'scope'  => $scope,
                        'data'   => $frenchTextCollectionValues,
                    ],
                ],
            ],
        ];

        $this->getDataLoader()->createProduct($identifier, $data);
    }

    private function createFamilies()
    {
        $this->getDataLoader()->createFamily(
            [
                'code'       => 'family_one',
                'attributes' => [
                    'name',
                ],
            ]
        );

        $this->getDataLoader()->createFamily(
            [
                'code'                   => 'family_two',
                'attributes'             => [
                    'name',
                    'my_text_collection',
                ],
                'attribute_requirements' => [
                    'ecommerce' => ['name', 'my_text_collection'],
                    'mobile'    => ['name', 'my_text_collection'],
                ],
            ]
        );
    }
}
