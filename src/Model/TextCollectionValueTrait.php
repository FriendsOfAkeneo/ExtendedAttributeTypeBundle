<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait to reuse in the overridden ProductValue on the dedicated project
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
trait TextCollectionValueTrait
{
    /** @var ArrayCollection */
    protected $textCollection;

    /**
     * @return ArrayCollection
     */
    public function getTextCollection()
    {
        return $this->textCollection;
    }

    /**
     * @param string[]|null $collection
     */
    public function setTextCollection($collection)
    {
        $this->textCollection = $collection;
    }

    /**
     * @param string $value
     */
    public function addTextCollectionItem($value)
    {
        $this->textCollection[] = $value;
    }
}
