<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\UserBundle\Entity\User;
use Rbs\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DepoForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea', array(
                'required' => false
            ))
            ->add('users', 'entity', array(
                'class' => 'Rbs\Bundle\UserBundle\Entity\User',
                'query_builder' => function(UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder('u')
                        ->andWhere("u.userType = :USER")
                        ->setParameters(array('USER' => User::USER));
                },
                'property' => 'username',
                'multiple' => true,
                'required' => false
            ))
            ->add('location', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Location',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.level = :level')->setParameter('level', 4)->orderBy('a.name');
                },
                'attr' => array(
                    'class' => 'zilla-selector select2me',
                    'id' => 'user_level1'
                ),
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Zilla should not be blank'
                    )),
                ),
                'empty_value' => 'Select Zilla',
                'required' => true
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\Depo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'depo';
    }
}
