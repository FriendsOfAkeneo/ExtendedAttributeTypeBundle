<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Elasticsearch;

use Akeneo\Bundle\ElasticsearchBundle\Client;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\AbstractTestCase;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductProposalTextCollectionFilterTest extends AbstractTestCase
{
    const DOCUMENT_TYPE = 'pimee_workflow_product_proposal';
    const PAGE_SIZE = 100;

    /** @var Client */
    protected $esProposalProductClient;

    /** @var ProductQueryBuilderFactoryInterface */
    protected $pqbFactory;

    /** @var AttributeInterface */
    protected $textCollAttribute;

    /**
     * {@inhritdoc}
     */
    protected function setUp()
    {
        if ('enterprise' !== static::getEdition()) {
            $this->markTestSkipped('Only relevant for enterprise edition');

            return;
        }
        parent::setUp();

        $this->esProposalProductClient = $this->get('akeneo_elasticsearch.client.product_proposal');
        $this->pqbFactory = $this->get('pimee_workflow.query.product_proposal_query_builder_factory');

        $this->getDataLoader()->activateLocales(['fr_FR']);
        $this->getDataLoader()->createAttribute(
            [
                'code'        => 'my_collection',
                'type'        => ExtendedAttributeTypes::TEXT_COLLECTION,
                'localizable' => true,
            ]
        );
        $this->addDocuments();
    }

    public function testProposalForTextCollectionValueIsEmpty()
    {
        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_collection', Operators::IS_EMPTY, null);
        $pqb->addSorter('sku', 'ASC');
        $results = $this->getSearchQueryResults($pqb->getQueryBuilder()->getQuery());

        $this->assertEquals(['product_1', 'product_2', 'product_6'], $results);
    }

    public function testProposalForTextCollectionIsNotEmpty()
    {
        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_collection', Operators::IS_NOT_EMPTY, null);
        $results = $this->getSearchQueryResults($pqb->getQueryBuilder()->getQuery());

        $this->assertEquals(['product_3', 'product_4', 'product_5'], $results);
    }

    public function testProposalForTextCollectionContainsItem()
    {
        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_collection', Operators::CONTAINS, 'an_item');
        $results = $this->getSearchQueryResults($pqb->getQueryBuilder()->getQuery());

        $this->assertEquals(['product_4', 'product_5'], $results);

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_collection', Operators::CONTAINS, 'item');
        $results = $this->getSearchQueryResults($pqb->getQueryBuilder()->getQuery());

        $this->assertCount(0, $results);

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_collection', Operators::CONTAINS, 'http://my/uri');
        $results = $this->getSearchQueryResults($pqb->getQueryBuilder()->getQuery());

        $this->assertEquals(['product_5'], $results);
    }

    public function testProposalForTextCollectionDoesNotContainItem()
    {
        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_collection', Operators::DOES_NOT_CONTAIN, 'an_item');
        $results = $this->getSearchQueryResults($pqb->getQueryBuilder()->getQuery());

        $this->assertEquals(['product_3'], $results);

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_collection', Operators::DOES_NOT_CONTAIN, 'http');
        $results = $this->getSearchQueryResults($pqb->getQueryBuilder()->getQuery());

        $this->assertEquals(['product_3', 'product_4', 'product_5'], $results);

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter('my_collection', Operators::DOES_NOT_CONTAIN, 'http://my/uri');
        $results = $this->getSearchQueryResults($pqb->getQueryBuilder()->getQuery());

        $this->assertEquals(['product_3', 'product_4'], $results);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return $this->catalog->useMinimalCatalog();
    }

    /**
     * {@inheritdoc}
     */
    protected function addDocuments()
    {
        $products = [
            [
                'identifier' => 'product_1',
                'values'     => [
                    'my_collection-textCollection' => [
                        '<all_channels>' => [
                            'en_US' => [],
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_2',
                'values'     => [
                    'my_collection-textCollection' => [
                        '<all_channels>' => [
                            'en_US' => [],
                            'fr_FR' => [],
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_3',
                'values'     => [
                    'my_collection-textCollection' => [
                        '<all_channels>' => [
                            'en_US' => ['http://not/empty'],
                            'fr_FR' => [],
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_4',
                'values'     => [
                    'my_collection-textCollection' => [
                        '<all_channels>' => [
                            'en_US' => [],
                            'fr_FR' => ['an_item', 'another_item'],
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_5',
                'values'     => [
                    'my_collection-textCollection' => [
                        '<all_channels>' => [
                            'en_US' => ['an_item', 'http://my/uri'],
                            'fr_FR' => ['another_item'],
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_6',
                'values'     => [
                    'name-text' => [
                        '<all_channels>' => [
                            'en_US' => 'Product number 6',
                            'fr_FR' => null,
                        ],
                    ],
                ],
            ],
        ];

        $this->indexDocuments($products);
    }

    /**
     * Indexes the given list of products
     *
     * @param array $productProposals
     */
    protected function indexDocuments(array $productProposals)
    {
        foreach ($productProposals as $productProposal) {
            $this->esProposalProductClient->index(
                self::DOCUMENT_TYPE,
                $productProposal['identifier'],
                $productProposal
            );
        }

        $this->esProposalProductClient->refreshIndex();
    }

    /**
     * Executes the given query and returns the list of skus found.
     *
     * @param array $query
     *
     * @return array
     */
    protected function getSearchQueryResults(array $query)
    {
        $identifiers = [];

        $query['size'] = self::PAGE_SIZE;
        $response = $this->esProposalProductClient->search(self::DOCUMENT_TYPE, $query);

        foreach ($response['hits']['hits'] as $hit) {
            $identifiers[] = $hit['_source']['identifier'];
        }

        return $identifiers;
    }
}
