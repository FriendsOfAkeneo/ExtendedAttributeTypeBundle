<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Filter\Form\Type;

use Oro\Bundle\FilterBundle\Form\Type\Filter\NumberFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type used to render range attribute type in the product datagrid.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeFilterType extends AbstractType
{
    /** @staticvar string */
    const NAME = 'pim_extended_attribute_type_range_filter';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return NumberFilterType::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'data_type' => NumberFilterType::DATA_DECIMAL
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('max');
    }
}
