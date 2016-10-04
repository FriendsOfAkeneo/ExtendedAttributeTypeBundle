<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Doctrine\ORM;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\CompletenessGenerator as BaseCompletenessGenerator;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class CECompletenessGenerator extends BaseCompletenessGenerator
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
