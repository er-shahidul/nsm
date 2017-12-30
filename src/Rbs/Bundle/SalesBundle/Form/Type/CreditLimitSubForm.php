<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\CategoryRepository;
use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreditLimitSubForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'text', array(
                'mapped' => false
            ))
            ->add('startDate', 'date', array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                    'class' => 'date-picker'
                ),
                'mapped' => false
            ))
            ->add('endDate', 'date', array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                    'class' => 'date-picker'
                ),
                'mapped' => false
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\CreditLimit'
        ));
    }

    public function getName()
    {
        return 'credit_limit_sub';
    }
}
