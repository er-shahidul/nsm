<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AgentProfileForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', null, array(
                'label' => 'Full Name',
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('cellphone', null, array(
                'label' => 'Cell Phone',
                'constraints' => array(
                    new NotBlank()
                ),
                'attr' => array('class' => 'input-mask-phone')
            ))
            ->add('address', 'textarea', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Address should not be blank'
                    ))
                ),
                'required' => true
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\UserBundle\Entity\Profile'
        ));
    }

    public function getName()
    {
        return 'agent_profile';
    }
}
