<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\Incentive;
use Rbs\Bundle\UserBundle\Entity\User;

/**
 * Class IncentiveDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class IncentiveDatatable extends BaseDatatable
{
    private $user;
    protected $showAgentName;

    public function getLineFormatter()
    {
        /** @var Incentive $incentive
         * @return mixed
         */
        $formatter = function($line){
            $incentive = $this->em->getRepository('RbsSalesBundle:Incentive')->find($line['id']);
            $line["isActive"] = $incentive->isActive();
            if ($this->showAgentName) {
                $line["fullName"] = Agent::agentIdNameFormat($incentive->getAgent()->getAgentID(), $incentive->getAgent()->getUser()->getProfile()->getFullName());
            }
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

        /** @var User $user */
        $this->user = $this->securityToken->getToken()->getUser();
        $this->showAgentName = $this->user->getUserType() != User::AGENT;

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('incentives_list_ajax'),
            'type' => 'GET'
        ));

        if ($this->showAgentName) {
            $this->columnBuilder->add('agent.user.id', 'column', array('title' => 'Agent Name', 'render' => 'resolveAgentName'));
        }

        $this->columnBuilder
            ->add('createdAt', 'datetime', array('title' => 'Created Date','date_format' => 'LLL' ))
            ->add('amount', 'column', array('title' => 'Amount'))
            ->add('details', 'column', array('title' => 'Details'))
            ->add('type', 'column', array('title' => 'Type'))
            ->add('status', 'column', array('title' => 'Status'))
            ->add('isActive', 'virtual', array('visible' => false))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'actions' => array(
                    $this->makeActionButton('incentive_approve', array('id' => 'id'), 'ROLE_ADMIN', 'Approve', 'Approve', '', 'btn btn-primary btn-xs delete-list-btn', array('render_if' => array('isActive'))),
                    $this->makeActionButton('incentive_cancel', array('id' => 'id'), 'ROLE_ADMIN', 'Reject', 'Reject', '', 'btn btn-primary btn-xs delete-list-btn', array('render_if' => array('isActive'))),
                    array(
                        'route' => 'incentive_details',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'View',
                        'icon' => 'glyphicon',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'show-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                            'data-target' => "#ajaxHistory",
                            'data-toggle'=>"modal"
                        ),
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
        return 'Rbs\Bundle\SalesBundle\Entity\Incentive';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'incentive_datatable';
    }
}
