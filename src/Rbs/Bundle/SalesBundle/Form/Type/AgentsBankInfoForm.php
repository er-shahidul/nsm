<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\SalesBundle\Repository\OrderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class AgentsBankInfoForm extends AbstractType
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', null, array(
                'attr' => array(
                    'class' => 'input-small input-mask-amount'
                ),
                'constraints' => array(
                    new GreaterThan(array(
                        'value' => 0,
                        'message' => 'This value should be greater than {{ compared_value }}.'
                    ))
                )
            ))
            ->add('bankName')
            ->add('branchName')
            ->add('remark')
            ->add('orderRef', 'entity', array(
                'class' => 'RbsSalesBundle:Order',
                'attr' => array(
                    'class' => 'select2me'
                ),
                'property' => 'orderInfo',
                'required' => false,
                'empty_value' => 'Select Order',
                'empty_data' => null,
                'query_builder' => function (OrderRepository $repository)
                {
                    return $repository->createQueryBuilder('o')
                        ->join('o.agent', 'a')
                        ->join('a.user', 'u')
                        ->andWhere('u.id =:user')
                        ->setParameter('user', $this->user->getId());
                }
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\AgentsBankInfo'
        ));
    }

    public function getName()
    {
        return 'agents_bank_info';
    }
}
