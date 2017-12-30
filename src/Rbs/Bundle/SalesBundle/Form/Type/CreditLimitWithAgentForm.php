<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\CategoryRepository;
use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Request;

class CreditLimitWithAgentForm extends AbstractType
{
    /** @var Request */
    private $agentId;
    private $categoryId;

    public function __construct($agentId, $categoryId)
    {
        $this->agentId = $agentId;
        $this->categoryId = $categoryId;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'text', array(
                'attr' => array(
                    'class' => 'input-small input-mask-amount'
                )
            ))
            ->add('startDate', 'date', array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                    'class' => 'date-picker'
                )
            ))
            ->add('endDate', 'date', array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                    'class' => 'date-picker'
                )
            ))
            ->add('agent', 'entity', array(
                'class' => 'RbsSalesBundle:Agent',
                'attr' => array(
                    'class' => 'select2me'
                ),
                'property' => 'user.profile.fullName',
                'empty_data' => null,
                'query_builder' => function (AgentRepository $repository)
                {
                    return $repository->createQueryBuilder('a')
                        ->where('a.id = :agentId')
                        ->setParameter('agentId', $this->agentId);
                }
            ))
            ->add('category', 'entity', array(
                'class' => 'RbsCoreBundle:Category',
                'attr' => array(
                    'class' => 'select2me'
                ),
                'property' => 'categoryName',
                'empty_data' => null,
                'query_builder' => function (CategoryRepository $repository)
                {
                    return $repository->createQueryBuilder('c')
                        ->where('c.id = :categoryId')
                        ->setParameter('categoryId', $this->categoryId);
                }
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
