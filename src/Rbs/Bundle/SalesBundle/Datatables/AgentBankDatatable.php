<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\AgentBank;

/**
 * Class Agent Bank Datatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class AgentBankDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var AgentBank $agentBank
         * @return mixed
         */
        $formatter = function($line){
            $agentsBank = $this->em->getRepository('RbsSalesBundle:AgentBank')->find($line['id']);
            $line['agentId'] = $agentsBank->getAgent()->getAgentID();
            $line['fullName'] = $agentsBank->getAgent()->getUser()->getProfile()->getFullName();

            return $line;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('agent_banks_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('code', 'column', array('title' => 'Bank Code'))
            ->add('agent.agentID', 'column', array('title' => 'Agent ID'))
            ->add('fullName', 'virtual', array('title' => 'Agent name'))
            ->add('bank', 'column', array('title' => 'Bank'))
            ->add('branch', 'column', array('title' => 'Branch'))
            ->add('cellphone', 'column', array('title' => 'Cell Phone'))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'Update',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'agent_bank_update',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Edit',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'edit-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_ADMIN',
                    )
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\AgentBank';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'agent_bank_datatable';
    }
}
