<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TargetSubForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'text')
            ->add('startDate', 'text', array(
                'attr' => array(
                    'class' => 'form-control month-year-picker'
                )
            ))
            ->add('endDate', 'text', array(
                'attr' => array(
                    'class' => 'form-control month-year-picker'
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\Target'
        ));
    }

    public function getName()
    {
        return 'target_sub_form';
    }
}
