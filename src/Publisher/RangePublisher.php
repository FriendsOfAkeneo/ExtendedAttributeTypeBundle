<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Publisher;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\PublishedProductRange;
use Pim\Bundle\ExtendedAttributeTypeBundle\Model\RangeInterface;
use PimEnterprise\Component\Workflow\Publisher\PublisherInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangePublisher implements PublisherInterface
{
    /**
     * {@inheritdoc}
     */
    public function publish($object, array $options = [])
    {
        $copiedRange = new PublishedProductRange();
        $copiedRange->setMin($object->getMin());
        $copiedRange->setMax($object->getMax());

        return $copiedRange;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof RangeInterface;
    }
}
