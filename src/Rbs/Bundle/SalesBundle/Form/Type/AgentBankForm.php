<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Rbs\Bundle\SalesBundle\Repository\AgentRepository;

class AgentBankForm extends AbstractType
{
    private $agent;

    public function __construct($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($this->agent == null){
            $builder
                ->add('agent', 'entity', array(
                    'class' => 'RbsSalesBundle:Agent',
                    'property' => 'getIdName',
                    'required' => true,
                    'attr' => array(
                        'class' => 'select2me'
                    ),
                    'empty_value' => 'Select Agent',
                    'empty_data' => null,
                ));
        }else{
            $builder
                ->add('agent', 'entity', array(
                    'class' => 'RbsSalesBundle:Agent',
                    'property' => 'getIdName',
                    'required' => true,
                    'empty_data' => null,
                    'query_builder' => function (AgentRepository $repository)
                    {
                        return $repository->createQueryBuilder('a')
                            ->andWhere("a IN(:agent)")
                            ->setParameter('agent', $this->agent);
                    }
                ));
        }
        $builder
            ->add('bank', 'text', array(
                'label' => 'Bank',
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Bank should not be blank'
                    ))
                )
            ))
            ->add('branch', 'text', array(
                'label' => 'Branch',
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Branch should not be blank'
                    ))
                )
            ))
            ->add('cellphone', 'text', array(
                'label' => 'Cell Phone',
                'required' => false,
                'attr' => array('class' => 'input-mask-phone')
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\AgentBank'
        ));
    }

    public function getName()
    {
        return 'agent_bank';
    }
}
