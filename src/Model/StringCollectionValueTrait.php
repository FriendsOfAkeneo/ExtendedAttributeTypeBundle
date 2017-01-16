<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait to reuse in the overridden ProductValue on the dedicated project
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
trait StringCollectionValueTrait
{
    /** @var ArrayCollection */
    protected $stringCollection;

    /**
     * @return ArrayCollection
     */
    public function getStringCollection()
    {
        return $this->stringCollection;
    }

    /**
     * @param array $collection
     */
    public function setStringCollection(array $collection)
    {
        $this->stringCollection = $collection;
    }

    /**
     * @param string $value
     */
    public function addStringCollectionItem($value)
    {
        $this->stringCollection[] = $value;
    }
}
