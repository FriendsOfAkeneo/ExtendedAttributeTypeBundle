<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Filter;

use Akeneo\Platform\Bundle\UIBundle\Provider\Filter\FilterProviderInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;

/**
 * Filter provider for text collection attribute
 *
 * This provider is registered via DI tag to add the text collection filter.
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FilterProvider implements FilterProviderInterface
{
    /** @var array */
    protected $filters = [
        ExtendedAttributeTypes::TEXT_COLLECTION => [
            'product-export-builder' => 'akeneo-attribute-text-collection-filter',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getFilters($attribute)
    {
        return $this->filters[$attribute->getAttributeType()];
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        return $element instanceof AttributeInterface &&
            in_array($element->getType(), array_keys($this->filters));
    }
}
