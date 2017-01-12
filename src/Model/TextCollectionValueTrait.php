<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait to reuse in the overridden ProductValue on the dedicated project
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 *
 * @property string[] textCollection
 */
trait TextCollectionValueTrait
{
    /**
     * @return ArrayCollection
     */
    public function getTextCollection()
    {
        return $this->textCollection;
    }

    /**
     * @param ArrayCollection $collection
     */
    public function setTextCollection(ArrayCollection $collection)
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
