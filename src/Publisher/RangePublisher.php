<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Publisher;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\PublishProductRange;
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
        $copiedRange = new PublishProductRange();
        $copiedRange->setMinData($object->getMinData());
        $copiedRange->setMaxData($object->getMaxData());

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
