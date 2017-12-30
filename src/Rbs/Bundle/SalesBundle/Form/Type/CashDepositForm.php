<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\UserBundle\Entity\User;
use Rbs\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CashDepositForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deposit', null, array(
                'attr' => array(
                    'class' => 'input-small input-mask-amount'
                )
            ))
            ->add('bankName')
            ->add('branchName')
            ->add('remark')
            ->add('depositedAt', 'date', array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                    'class' => 'date-picker'
                )
            ))
            ->add('depositedBy', 'entity', array(
                'class' => 'Rbs\Bundle\UserBundle\Entity\User',
                'attr' => array(
                    'class' => 'select2me'
                ),
                'query_builder' => function(UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder('u')
                        ->andWhere("u.userType = :USER")
                        ->setParameters(array('USER' => User::USER));
                },
                'property' => 'username'
            ))
            ->add('file')
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\CashDeposit'
        ));
    }

    public function getName()
    {
        return 'cash_deposit';
    }
}
