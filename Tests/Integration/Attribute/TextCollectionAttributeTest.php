<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\Attribute;

use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration\AbstractTestCase;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\FamilyInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionAttributeTest extends AbstractTestCase
{
    public function testCreateTextCollectionAttribute()
    {
        $attribute = $this->getDataLoader()->createAttribute(
            [
                'code' => 'my_text_collection',
                'type' => ExtendedAttributeTypes::TEXT_COLLECTION,
            ]
        );
        $this->clear();

        /** @var AttributeInterface $savedAttribute */
        $savedAttribute = $this->get('pim_catalog.repository.attribute')->findOneByIdentifier('my_text_collection');

        $this->assertNotSame($attribute, $savedAttribute);
        $this->assertEquals(ExtendedAttributeTypes::TEXT_COLLECTION, $savedAttribute->getType());
        $this->assertEquals(ExtendedAttributeTypes::BACKEND_TYPE_TEXT_COLLECTION, $savedAttribute->getBackendType());
        $this->assertFalse($savedAttribute->isScopable());
        $this->assertFalse($savedAttribute->isLocalizable());
        $this->assertNull($savedAttribute->isWysiwygEnabled());
    }

    public function testCreateAttributeWithInvalidData()
    {
        $this->expectException(InvalidPropertyTypeException::class);
        $this->getDataLoader()->createAttribute(
            [
                'code'   => 'my_text_collection',
                'type'   => ExtendedAttributeTypes::TEXT_COLLECTION,
                'labels' => 'not_an_array',
            ]
        );
    }

    public function testUpdateTextCollectionAttribute()
    {
        $attribute = $this->getDataLoader()->createAttribute(
            [
                'code' => 'my_text_collection',
                'type' => ExtendedAttributeTypes::TEXT_COLLECTION,
            ]
        );
        $this->assertCount(0, $attribute->getTranslations());
        $this->assertFalse($attribute->isScopable());

        $this->get('pim_catalog.updater.attribute')->update(
            $attribute,
            [
                'labels'                 => [
                    'en_US' => 'My text collection',
                ],
                'useable_as_grid_filter' => true,
                'scopable'               => true,
            ]
        );
        $this->get('pim_catalog.saver.attribute')->save($attribute);
        $this->clear();

        /** @var AttributeInterface $savedAttribute */
        $savedAttribute = $this->get('pim_catalog.repository.attribute')->findOneByIdentifier('my_text_collection');

        $this->assertNotSame($attribute, $savedAttribute);
        $this->assertCount(1, $savedAttribute->getTranslations());
        $this->assertEquals('en_US', $savedAttribute->getTranslations()->first()->getLocale());
        $this->assertEquals('My text collection', $savedAttribute->getTranslations()->first()->getLabel());
        $this->assertTrue($savedAttribute->isScopable());
    }

    public function testDeleteAttribute()
    {
        $this->getDataLoader()->createAttribute(
            [
                'code' => 'my_text_collection',
                'type' => ExtendedAttributeTypes::TEXT_COLLECTION,
            ]
        );
        $this->clear();
        $savedAttribute = $this->get('pim_catalog.repository.attribute')->findOneByIdentifier('my_text_collection');
        $this->assertInstanceOf(AttributeInterface::class, $savedAttribute);

        $this->get('pim_catalog.remover.attribute')->remove($savedAttribute);
        $this->clear();

        $this->assertNull($this->get('pim_catalog.repository.attribute')->findOneByIdentifier('my_text_collection'));
    }

    public function testDeleteAttributeInFamily()
    {
        $this->getDataLoader()->createAttribute(
            [
                'code' => 'my_text_collection',
                'type' => ExtendedAttributeTypes::TEXT_COLLECTION,
            ]
        );
        $this->getDataLoader()->createFamily([
            'code' => 'my_family',
            'attributes' => [
                'my_text_collection',
            ],
        ]);
        $this->clear();

        $savedFamily = $this->get('pim_catalog.repository.family')->findOneByIdentifier('my_family');
        $this->assertInstanceOf(FamilyInterface::class, $savedFamily);

        $this->assertCount(2, $savedFamily->getAttributeCodes());
        $this->assertContains('my_text_collection', $savedFamily->getAttributeCodes());
        $savedAttribute = $this->get('pim_catalog.repository.attribute')->findOneByIdentifier('my_text_collection');
        $this->get('pim_catalog.remover.attribute')->remove($savedAttribute);

        $this->clear();

        $updatedFamily = $this->get('pim_catalog.repository.family')->findOneByIdentifier('my_family');
        $this->assertInstanceOf(FamilyInterface::class, $updatedFamily);

        $this->assertCount(1, $updatedFamily->getAttributeCodes());
        $this->assertNotContains('my_text_collection', $updatedFamily->getAttributeCodes());
        $this->assertNull($this->get('pim_catalog.repository.attribute')->findOneByIdentifier('my_text_collection'));
    }
}
