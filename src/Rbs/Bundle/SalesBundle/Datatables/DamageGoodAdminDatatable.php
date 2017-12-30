<?php

namespace Rbs\Bundle\SalesBundle\Datatables;
use Rbs\Bundle\SalesBundle\Entity\DamageGood;

/**
 * Class DamageGoodDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class DamageGoodAdminDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var DamageGood $damageGood
         * @return mixed
         */
        $formatter = function($line){
            /** @var DamageGood $damageGood */
            $damageGood = $this->em->getRepository('RbsSalesBundle:DamageGood')->find($line['id']);
            $line['isPathExist'] = !$damageGood->isPathExist();
            $line['isApproved'] = $damageGood->isApproved();
            $line['attachFile'] = empty($line['path']) ? '' : '<a href="'.$damageGood->getDownloadFilePath().'" rel="tooltip" title="view" class="btn btn-xs" role="button" target="_blank"><i class="fa fa-file"></i> View</a>';

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
            'url' => $this->router->generate('damage_good_admin_list_ajax'),
            'type' => 'GET'
        ));

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->columnBuilder
            ->add('createdAt', 'datetime', array('title' => 'Date', 'date_format' => $dateFormat))
            ->add('user.username', 'column', array('title' => 'User'))
            ->add('agent.user.username', 'column', array('title' => 'Agent'))
            ->add('orderRef.id', 'column', array('title' => 'Order Number'))
            ->add('remark', 'column', array('title' => 'Remark'))
            ->add('status', 'column', array('title' => 'Status'))
            ->add('amount', 'column', array('title' => 'Claim'))
            ->add('refundAmount', 'column', array('title' => 'Refund'))
            ->add('isPathExist', 'virtual', array('visible' => false))
            ->add('path', 'column', array('visible' => false))
            ->add('attachFile', 'virtual', array('title' => 'File'))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'actions' => array(
                    $this->makeActionButton('damage_goods_verify', array('id' => 'id'), 'ROLE_DAMAGE_GOODS_VERIFY', 'Verify', 'Verify', 'fa fa-thumbs-up', 'btn btn-primary btn-xs delete-list-btn'),
                    $this->makeActionButton('damage_goods_view', array('id' => 'id'), 'ROLE_DAMAGE_GOODS_APPROVE', 'View', 'View', 'fa fa-eye'),
                    array(
                        'route' => 'damage_goods_reject_form',
                        'route_parameters' => array('id' => 'id'),
                        'label' => 'Reject',
                        'icon' => 'fa fa-thumbs-down',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Reject',
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'button',
                            'data-toggle' => 'modal',
                            'data-target' => '#damageGoodReject'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_DAMAGE_GOODS_VERIFY',
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
        return 'Rbs\Bundle\SalesBundle\Entity\DamageGood';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'damage_good_datatable';
    }
}
