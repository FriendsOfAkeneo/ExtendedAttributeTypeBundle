<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type related to range entity (to be used in the product datagrid).
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', 'pim_number', ['label' => 'Min'])
            ->add('max', 'pim_number', ['label' => 'Max']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_form_type_range';
    }
}
