<?php

namespace Acme\Bundle\AppEEBundle\Doctrine\ORM;

use PimEnterprise\Bundle\CatalogBundle\Doctrine\ORM\CompletenessGenerator as BaseCompletenessGenerator;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class EECompletenessGenerator extends BaseCompletenessGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function getClassContentFields($className, $prefix)
    {
        if ($className === 'Pim\Bundle\ExtendedAttributeTypeBundle\Model\ProductRange') {
            return [sprintf('(%s.%s AND %s.%s)', $prefix, 'min', $prefix, 'max')];
        }

        return parent::getClassContentFields($className, $prefix);
    }
}
