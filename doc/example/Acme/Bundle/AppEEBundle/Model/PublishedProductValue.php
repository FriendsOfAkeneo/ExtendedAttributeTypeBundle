<?php

namespace Acme\Bundle\AppEEBundle\Model;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\TextCollectionValueTrait;
use PimEnterprise\Component\Workflow\Model\PublishedProductValue as PimPublishedProductValue;

/**
 * Overrides the published product value to take "range" attribute
 * type into account.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class PublishedProductValue extends PimPublishedProductValue
{
    use TextCollectionValueTrait;
}
