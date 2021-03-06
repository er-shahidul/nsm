<?php

namespace Rbs\Bundle\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\UserBundle\Entity\User;
use Rbs\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserUpdateForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'form.email', 'translation_domain' => 'FOSUserBundle',
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Email should not be blank'
                    )),
                    new email()
                ),
            ))
            ->add('userType', 'choice', array(
                'choices'  => array(
                    'USER' => User::USER,
                    'ZM' => User::ZM,
                    'RSM' => User::RSM,
                    'SR' => User::SR,
                    'AGENT' => User::AGENT
                )
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
            'data_class' => 'Rbs\Bundle\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
