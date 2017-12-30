<?php

namespace Rbs\Bundle\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\UserBundle\Entity\User;
use Rbs\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class UserForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle',
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Username should not be blank'
                    ))
                ),
            ))
            ->add('userType', 'choice', array(
                'choices'  => array(
                    'USER' => User::USER,
                    'ZM' => User::ZM,
                    'RSM' => User::RSM,
                    'SR' => User::SR,
                    'AGENT' => User::AGENT
                ),
                'data' => User::USER
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array(
                    'label' => 'form.password_confirmation',
                    'constraints' => new NotBlank()
                ),
                'invalid_message' => 'fos_user.password.mismatch',
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Password should not be blank'
                    )),
                    new Length(array('min' => 6)),
                ),
            ))
            ->add('email', 'email', array(
                'label' => 'form.email', 'translation_domain' => 'FOSUserBundle',
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Email should not be blank'
                    )),
                    new email()
                ),
            ))
            ->add('parentId', 'entity', array(
                'class' => 'Rbs\Bundle\UserBundle\Entity\User',
                'query_builder' => function(UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder('u')
                        ->join("u.profile", "p")
                        ->where("u.userType != :AGENT")
                        ->andWhere('u.deletedAt IS NULL')
                        ->setParameters(array('AGENT'=> User::AGENT));
                },
                'property' => 'profile.fullname',
                'required' => false,
                'empty_value' => 'Select Parent',
            ))
            ->add('zilla', 'entity', array(
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
                'required' => true
            ))
            ->add('upozilla', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Location',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.level = :level')->setParameter('level', 5)->orderBy('a.name');
                },
                'attr' => array(
                    'class' => 'thana-selector select2me',
                    'id' => 'user_level2'
                ),
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Upozilla should not be blank'
                    )),
                ),
                'required' => true
            ))
        ;

        $builder
            ->add('profile', new ProfileForm());

        $builder
            ->add('groups', 'entity', array(
                'class' => 'Rbs\Bundle\UserBundle\Entity\Group',
                'query_builder' => function(EntityRepository $groupRepository) {
                    return $groupRepository->createQueryBuilder('g')
                        ->andWhere("g.name != :group")
                        ->orderBy('g.name', 'ASC')
                        ->setParameter('group', 'Super Administrator');
                },
                'property' => 'name',
                'multiple' => true,
                'required' => false
            ))
        ;

        $builder
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
            'data_class' => 'Rbs\Bundle\UserBundle\Entity\User',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
