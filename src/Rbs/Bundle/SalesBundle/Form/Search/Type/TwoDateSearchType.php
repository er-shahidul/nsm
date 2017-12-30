<?php

namespace Rbs\Bundle\SalesBundle\Form\Search\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwoDateSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_date', 'text', array(
                'attr' => array(
                    'class' => 'date-picker input-small form-control'
                )
            ))
            ->add('end_date', 'text', array(
                'attr' => array(
                    'class' => 'date-picker input-small form-control'
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

        ));
    }

    public function getName()
    {
        return 'search';
    }
}
