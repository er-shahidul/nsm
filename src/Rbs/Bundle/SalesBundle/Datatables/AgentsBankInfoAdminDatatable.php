<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\AgentsBankInfo;

/**
 * Class AgentsBankInfoDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class AgentsBankInfoAdminDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var AgentsBankInfo $agentsBankInfo
         * @return mixed
         */
        $formatter = function($line){
            $agentsBankInfo = $this->em->getRepository('RbsSalesBundle:AgentsBankInfo')->find($line['id']);
            $line['isPathExist'] = !$agentsBankInfo->isPathExist();
            $line['isApproved'] = !$agentsBankInfo->isApproved();
            $line['isVerified'] = !$agentsBankInfo->isVerified();
            $line['isVerifiedTrue'] = $agentsBankInfo->isVerified();
            $line['isCancel'] = !$agentsBankInfo->isCancel();

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
            'url' => $this->router->generate('bank_info_list_ajax'),
            'type' => 'GET'
        ));

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->columnBuilder
            ->add('createdAt', 'datetime', array('title' => 'Date', 'date_format' => $dateFormat))
            ->add('orderRef.id', 'column', array('title' => 'Order Number'))
            ->add('bankName', 'column', array('title' => 'Bank Name'))
            ->add('branchName', 'column', array('title' => 'Branch Name'))
            ->add('remark', 'column', array('title' => 'Remark'))
            ->add('amount', 'column', array('title' => 'Amount'))
            ->add('status', 'column', array('title' => 'Status'))
            ->add('isPathExist', 'virtual', array('visible' => false))
            ->add('isApproved', 'virtual', array('visible' => false))
            ->add('isVerified', 'virtual', array('visible' => false))
            ->add('isVerifiedTrue', 'virtual', array('visible' => false))
            ->add('path', 'column', array('visible' => false))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'File',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'agent_bank_info_doc_view',
                        'route_parameters' => array(
                            'path' => 'path'
                        ),
                        'label' => 'View',
                        'icon' => 'fa fa-file',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'view',
                            'class' => 'btn btn-xs',
                            'role' => 'button',
                            'target'=> '_blank'
                        ),
                        'render_if' => array('isPathExist')
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
                        'route' => 'agent_bank_info_verify',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Verify',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'enable-action',
                            'class' => 'btn btn-primary btn-xs delete-list-btn',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_BANK_SLIP_VERIFIER',
                        'render_if' => array('isCancel', 'isVerified')
                    ),
                    array(
                        'route' => 'agent_bank_info_approve',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Approve',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'disable-action',
                            'class' => 'btn btn-primary btn-xs delete-list-btn',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_BANK_SLIP_APPROVAL',
                        'render_if' => array('isCancel', 'isVerifiedTrue', 'isApproved')
                    ),
                    array(
                        'route' => 'agent_bank_info_cancel',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Cancel',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'disable-action',
                            'class' => 'btn btn-primary btn-xs delete-list-btn',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_BANK_SLIP_APPROVAL',
                        'render_if' => array('isCancel', 'isApproved')
                    ),
                    array(
                        'route' => 'agent_bank_info_cancel',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Cancel',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'disable-action',
                            'class' => 'btn btn-primary btn-xs delete-list-btn',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_BANK_SLIP_VERIFIER',
                        'render_if' => array('isCancel', 'isVerified')
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
        return 'Rbs\Bundle\SalesBundle\Entity\AgentsBankInfo';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'agents_bank_info_datatable';
    }
}
