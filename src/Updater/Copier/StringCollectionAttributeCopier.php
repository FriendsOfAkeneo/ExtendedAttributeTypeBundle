<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Updater\Copier;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Updater\Copier\BaseAttributeCopier;

/**
 * Copy a string collection
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StringCollectionAttributeCopier extends BaseAttributeCopier
{
    /** @var string[] */
    protected $supportedFromTypes = [ExtendedAttributeTypes::STRING_COLLECTION];

    /** @var string[] */
    protected $supportedToTypes = [ExtendedAttributeTypes::STRING_COLLECTION];
}
