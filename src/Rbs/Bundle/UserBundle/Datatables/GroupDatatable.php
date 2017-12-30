<?php

namespace Rbs\Bundle\UserBundle\Datatables;

/**
 * Class UserDatatable
 *
 * @package Rbs\Bundle\UserBundle\Datatables
 */
class GroupDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('groups_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
                ->add('name', 'column', array('title' => 'Name','width' => '200px'))
                ->add('description', 'column', array('title' => 'Description',))
                ->add(null, 'action', array(
                    'width' => '170px',
                    'title' => 'Action',
                    'actions' => array(
                        array(
                            'route' => 'group_update',
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
                            'route' => 'group_delete',
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
                            'role' => 'ROLE_ADMIN',
                        )
                    )
                ))
//                ->add(null, 'action', array(
//                    'width' => '180px',
//                    'title' => 'Show',
//                    'start_html' => '<div class="wrapper">',
//                    'end_html' => '</div>',
//                    'actions' => array(
//                        array(
//                            'route' => 'group_details',
//                            'route_parameters' => array(
//                                'id' => 'id'
//                            ),
//                            'label' => 'Details',
//                            'icon' => 'glyphicon',
//                            'attributes' => array(
//                                'rel' => 'tooltip',
//                                'title' => 'details-action',
//                                'class' => 'btn btn-primary btn-xs',
//                                'role' => 'button'
//                            ),
//                            'role' => 'ROLE_ADMIN',
//                        )
//                    )
//                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\UserBundle\Entity\Group';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'group_datatable';
    }
}
