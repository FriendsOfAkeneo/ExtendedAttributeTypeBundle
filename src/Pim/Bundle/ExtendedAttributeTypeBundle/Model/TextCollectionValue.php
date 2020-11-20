<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

//use Pim\Component\Catalog\Model\AbstractValue;
//use Pim\Component\Catalog\Model\AttributeInterface;
//use Pim\Component\Catalog\Model\ValueInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\AbstractValue;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;

/**
 * Product value for TextCollection attribute type
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionValue extends AbstractValue
{
    /** @var string[] */
    protected $data;


    protected function __construct(string $attributeCode, $data, ?string $scopeCode, ?string $localeCode)
    {
        parent::__construct($attributeCode, $data, $scopeCode, $localeCode);
    }

    /**
     * @return string[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $item
     */
    public function removeItem(string $item)
    {
        $data = array_filter($this->data, function ($value) use ($item) {
            return $value !== $item;
        });
        $this->data = array_values($data);
    }

    public function isEqual(ValueInterface $value): bool
    {
        if (!$value instanceof TextCollectionValue ||
            $this->getScopeCode() !== $value->getScopeCode() ||
            $this->getLocaleCode() !== $value->getLocaleCode()) {
            return false;
        }

        $comparedAttributeOptions = $value->getData();
        $thisAttributeOptions = $this->getData();

        return count(array_diff($thisAttributeOptions, $comparedAttributeOptions)) === 0 &&
            count(array_diff($comparedAttributeOptions, $thisAttributeOptions)) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return implode(', ', $this->data);
    }
}
