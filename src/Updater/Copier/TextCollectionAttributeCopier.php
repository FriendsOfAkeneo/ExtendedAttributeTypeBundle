<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Updater\Copier;

use Pim\Bundle\ExtendedAttributeTypeBundle\AttributeType\ExtendedAttributeTypes;
use Pim\Component\Catalog\Updater\Copier\BaseAttributeCopier;

/**
 * Copy a text collection
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextCollectionAttributeCopier extends BaseAttributeCopier
{
    /** @var string[] */
    protected $supportedFromTypes = [ExtendedAttributeTypes::TEXT_COLLECTION];

    /** @var string[] */
    protected $supportedToTypes = [ExtendedAttributeTypes::TEXT_COLLECTION];
}
