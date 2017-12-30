<?php

namespace Rbs\Bundle\UserBundle\Datatables;

use FOS\UserBundle\Model\User;

/**
 * Class UserDatatable
 *
 * @package Rbs\Bundle\UserBundle\Datatables
 */
class UserDatatable extends BaseDatatable
{
    
    public function getLineFormatter()
    {
        /** @var User $user
         * @return mixed
         */
        $formatter = function($line){
            $user = $this->em->getRepository('RbsUserBundle:User')->find($line['id']);
            $line["isSuperAdmin"] = !$user->isSuperAdmin();
            $line['enabled'] = $user->isEnabled();
            $line['disabled'] = !$user->isEnabled();
            $line['sl'] = 0;

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
        $this->options->setOptions(array_merge($this->defaultOptions(), array('order' => [[0, 'asc']])));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('users_list_ajax'),
            'type' => 'GET'
        ));

        $this->callbacks->setCallbacks(array
            (
                'pre_draw_callback' => "function(settings){
                    dataTableSetting = settings;
                }",
                'row_callback' => "function(nRow, aData, iDisplayIndex){
                    $('td:first', nRow).html(dataTableSetting._iDisplayStart + iDisplayIndex + 1);
                    return nRow;
                }"
            )
        );

        $this->columnBuilder
                ->add('sl', 'virtual', array('title' => 'Sl',))
                ->add('username', 'column', array('title' => 'Username',))
                ->add('profile.fullName', 'column', array('title' => 'Full Name',))
                ->add('userType', 'column', array('title' => 'User Type',))
                ->add('profile.cellphone', 'column', array('title' => 'Cell Phone',))
                ->add('profile.designation', 'column', array('title' => 'Designation',))
                ->add('isSuperAdmin', 'virtual', array('visible' => false))
                ->add('enabled', 'virtual', array('visible' => false))
                ->add('disabled', 'virtual', array('visible' => false))
                ->add(null, 'action', array(
                    'width' => '180px',
                    'title' => 'Update',
                    'start_html' => '<div class="wrapper">',
                    'end_html' => '</div>',
                    'actions' => array(
                        array(
                            'route' => 'user_update',
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
                            'route' => 'user_update_password',
                            'route_parameters' => array(
                                'id' => 'id'
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
                            'confirm_message' => 'Are you sure?',
                            'role' => 'ROLE_ADMIN',
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
                            'route' => 'user_delete',
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
                            'role' => 'ROLE_ADMIN',
                            'render_if' => array('isSuperAdmin')
                        ),
                        array(
                            'route' => 'user_enabled',
                            'route_parameters' => array(
                                'id' => 'id'
                            ),
                            'label' => 'Enable',
                            'icon' => 'glyphicon glyphicon-edit',
                            'attributes' => array(
                                'rel' => 'tooltip',
                                'title' => 'enable-action',
                                'class' => 'btn btn-primary btn-xs delete-list-btn',
                                'role' => 'button'
                            ),
                            'confirm' => false,
                            'confirm_message' => 'Are you sure?',
                            'role' => 'ROLE_ADMIN',
                            'render_if' => array('disabled', 'isSuperAdmin')
                        ),
                        array(
                            'route' => 'user_enabled',
                            'route_parameters' => array(
                                'id' => 'id'
                            ),
                            'label' => 'Disable',
                            'icon' => 'glyphicon glyphicon-edit',
                            'attributes' => array(
                                'rel' => 'tooltip',
                                'title' => 'disable-action',
                                'class' => 'btn btn-primary btn-xs delete-list-btn',
                                'role' => 'button'
                            ),
                            'confirm' => false,
                            'confirm_message' => 'Are you sure?',
                            'role' => 'ROLE_ADMIN',
                            'render_if' => array('enabled', 'isSuperAdmin')
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
        return 'Rbs\Bundle\UserBundle\Entity\User';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_datatable';
    }
}
