<?php

namespace Rbs\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', 'text', array(
                'constraints' => array(
                )
            ))
            ->add('cellphone', 'text', array(
                'constraints' => array(
                    new Regex(array(
                        'pattern'   => '/^(\+?\(?\d{2,4}\)?[\d\s-]{3,})$/',
                        'match'     => true,
                        'message' =>'Wrong phone number'
                    ))
                ),
                'attr' => array(
                    'class' => 'input-mask-phone'
                )
            ))
            ->add('designation')
            ->add('address', 'text', array(
                'required' => false
            ))
            ->add('file')
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
        return 'user_profile';
    }
}
