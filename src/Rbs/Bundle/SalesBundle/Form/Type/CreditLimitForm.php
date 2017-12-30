<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreditLimitForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('agent', 'entity', array(
                'class' => 'RbsSalesBundle:Agent',
                'property' => 'getIdName',
                'required' => false,
                'attr' => array(
                    'class' => 'select2me'
                ),
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Agent should not be blank'
                    )),
                ),
                'empty_value' => 'Select Agent',
                'query_builder' => function (AgentRepository $repository)
                {
                    return $repository->createQueryBuilder('c')
                        ->join('c.user', 'u')
                        ->join('u.profile', 'p')
                        ->where('u.deletedAt IS NULL')
                        ->andWhere('u.enabled = 1')
                        ->andWhere('u.userType = :AGENT')
                        ->setParameter('AGENT', User::AGENT)
                        ->orderBy('p.fullName','ASC');
                }
            ))
            ->add('child_entities', 'collection', array(
                'type' => new CreditLimitSubForm(),
            ))
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\CreditLimit'
        ));
    }

    public function getName()
    {
        return 'credit_limit';
    }
}
