<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\Agent;

/**
 * Class AgentDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class AgentDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('agents_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('agentID', 'column', array('title' => 'Agent ID'))
            ->add('user.profile.fullName', 'column', array('title' => 'Agent name'))
            ->add('user.profile.cellphone', 'column', array('title' => 'Cell Phone'))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'Update',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'agent_update',
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
                    ),
                    array(
                        'route' => 'agent_update_password',
                        'route_parameters' => array(
                            'id' => 'user.id'
                        ),
                        'label' => 'Edit pass',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'edit-password-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?'
                    )
                )
            ))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'agent_delete',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Delete',
                        'icon' => 'glyphicon',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'delete-action',
                            'class' => 'btn btn-default btn-xs delete-list-btn',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_ADMIN'
                    ),
                    array(
                        'route' => 'agent_details',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Show',
                        'icon' => 'glyphicon',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'show-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?'
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
        return 'Rbs\Bundle\SalesBundle\Entity\Agent';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'agent_datatable';
    }
}
