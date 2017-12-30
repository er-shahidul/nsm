<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

class TargetUpdateForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'text')
            ->add($builder->create('startDate', 'text', array(
                'attr' => array(
                    'class' => 'form-control month-year-picker'
                ),
                'data_class' => null
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))
            ->add($builder->create('endDate', 'text', array(
                'attr' => array(
                    'class' => 'form-control month-year-picker'
                ),
                'data_class' => null
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))
            ->add('submit', 'submit', array(
                'attr'     => array('class' => 'btn green')
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
        return 'target';
    }
}
