<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\DepoRepository;
use Rbs\Bundle\CoreBundle\Repository\ItemTypeRepository;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Repository\AgentGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgentUpdateForm extends AbstractType
{
    private $openingBalanceFlag;

    public function __construct($openingBalanceFlag)
    {
        $this->openingBalanceFlag = $openingBalanceFlag;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $agentId = $builder->getData()->getAgentID();
        $isAgentIdDisabled = false;//!empty($agentId);
        $builder
            ->add('VIP', 'choice', array(
                    'choices' => array(
                        '0' => 'No',
                        '1' => 'Yes',
                    ),
                    'label' => 'VIP'
                )
            )
            ->add(
                'itemType',
                'entity',
                array(
                    'class'         => 'RbsCoreBundle:ItemType',
                    'label'         => 'Item Type',
                    'property'      => 'itemType',
                    'required'      => false,
                    'empty_value'   => 'Select Item Type',
                    'empty_data'    => null,
                    'query_builder' => function (ItemTypeRepository $repository) {
                        return $repository->createQueryBuilder('it')
                            ->where('it.deletedAt IS NULL')
                            ->orderBy('it.itemType', 'ASC');
                    },
                )
            )
            ->add(
                'agentID', null, array(
                    'label' => 'Agent ID',
                    'disabled' => $isAgentIdDisabled
                )
            );

        if (!$this->openingBalanceFlag) {
            $builder
                ->add('openingBalance')
                ->add(
                    'openingBalanceType',
                    'choice',
                    array(
                        'choices' => array(
                            'CR' => Agent::CR,
                            'DR' => Agent::DR,
                        ),
                    )
                );
        }

        $builder
            ->add(
                'depo',
                'entity',
                array(
                    'class'         => 'RbsCoreBundle:Depo',
                    'property'      => 'name',
                    'required'      => true,
                    'empty_value'   => 'Select Depo',
                    'empty_data'    => null,
                    'query_builder' => function (DepoRepository $repository) {
                        return $repository->createQueryBuilder('w')
                            ->where('w.deletedAt IS NULL')
                            ->orderBy('w.name', 'ASC');
                    },
                )
            )
            ->add(
                'agentGroup',
                'entity',
                array(
                    'class'         => 'RbsSalesBundle:AgentGroup',
                    'label'         => 'Agent Group',
                    'property'      => 'label',
                    'required'      => false,
                    'empty_value'   => 'Select Group',
                    'empty_data'    => null,
                    'query_builder' => function (AgentGroupRepository $repository) {
                        return $repository->createQueryBuilder('cg')
                            ->where('cg.deletedAt IS NULL')
                            ->orderBy('cg.label', 'ASC');
                    },
                )
            );

        $builder
            ->add('user', new UserAgentUpdateForm());

        $builder
            ->add(
                'submit',
                'submit',
                array(
                    'attr' => array('class' => 'btn green'),
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\Agent'
        ));
    }

    public function getName()
    {
        return 'user_agent';
    }
}
