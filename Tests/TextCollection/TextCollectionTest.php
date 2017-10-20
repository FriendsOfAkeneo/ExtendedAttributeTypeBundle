<?php

namespace Tests\TextCollection;

use Pim\Behat\Context\DBALPurger;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Repository\AttributeGroupRepositoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TextCollectionTest extends KernelTestCase
{
    /** @var ContainerInterface */
    private $container;

    /** @var AttributeRepositoryInterface */
    private $attributeRepo;

    /** @var AttributeGroupRepositoryInterface */
    private $attributeGroupRepo;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $connection = $this->container->get('doctrine.dbal.default_connection');
        $purger = new DBALPurger($connection, ['pim_catalog_attribute_group', 'pim_catalog_attribute']);
        $purger->purge();

        $this->attributeRepo = $this->container->get('pim_catalog.repository.attribute');
        $this->attributeGroupRepo = $this->container->get('pim_catalog.repository.attribute_group');
    }

    public function testCreateAttribute()
    {
        $factory = $this->container->get('pim_catalog.factory.attribute');
        $attribute = $factory->createAttribute('pim_catalog_text_collection');
        $attribute->setCode('my_collection');
        $this->assertEquals(ExtendedAttributeTypes::TEXT_COLLECTION, $attribute->getType());
        $this->assertEquals(ExtendedAttributeTypes::BACKEND_TYPE_TEXT_COLLECTION, $attribute->getBackendType());

        $defaultGroup = $this->attributeGroupRepo->findDefaultAttributeGroup();
        $attribute->setGroup($defaultGroup);

        $saver = $this->container->get('pim_catalog.saver.attribute');
        $saver->save($attribute);
        unset($attributeGroupRepo);
        unset($attribute);

        /** @var AttributeInterface $savedAttribute */
        $savedAttribute = $this->attributeRepo->findOneByIdentifier('my_collection');
        $this->assertInstanceOf(AttributeInterface::class, $savedAttribute);
        $this->assertEquals(ExtendedAttributeTypes::TEXT_COLLECTION, $savedAttribute->getType());
        $this->assertEquals(ExtendedAttributeTypes::BACKEND_TYPE_TEXT_COLLECTION, $savedAttribute->getBackendType());
//        $this->assertEquals($defaultGroup->getCode(), $savedAttribute->getGroup()->getCode());
    }
}
