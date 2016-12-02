<?php

namespace Acme\Bundle\AppBundle\Model;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\RangeValueTrait;
use PimEnterprise\Component\Workflow\Model\PublishedProductValue as PimPublishedProductValue;


/**
 * Overrides the published product value to take "range" attribute
 * type into account.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class PublishedProductValue extends PimPublishedProductValue
{
    use RangeValueTrait;
}
