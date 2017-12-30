<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\AgentGroup;

/**
 * Class AgentGroupDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class AgentGroupDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('agent_groups_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('label', 'column', array('title' => 'Agent group name'))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'Action',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'agent_group_update',
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
                        'route' => 'agent_group_delete',
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
        return 'Rbs\Bundle\SalesBundle\Entity\AgentGroup';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'agent_group_datatable';
    }
}
