<?php

namespace Rbs\Bundle\SalesBundle\Datatables;
use Rbs\Bundle\SalesBundle\Entity\CashDeposit;

/**
 * Class CashDepositDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class CashDepositDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var CashDeposit $cashDeposit
         * @return mixed
         */
        $formatter = function($line){
            $cashDeposit = $this->em->getRepository('RbsSalesBundle:CashDeposit')->find($line['id']);
            $line['isPathExist'] = !$cashDeposit->isPathExist();

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
            'url' => $this->router->generate('cash_deposit_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('depositedAt', 'datetime', array('title' => 'Date', 'date_format' => 'LL' ))
            ->add('depo.name', 'column', array('title' => 'Depo'))
            ->add('depositedBy.username', 'column', array('title' => 'Depositor'))
            ->add('deposit', 'column', array('title' => 'Amount'))
            ->add('bankName', 'column', array('title' => 'Bank Name'))
            ->add('branchName', 'column', array('title' => 'Branch Name'))
            ->add('remark', 'column', array('title' => 'Remark'))
            ->add('isPathExist', 'virtual', array('visible' => false))
            ->add('path', 'column', array('visible' => false))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'File',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'cash_deposit_doc_view',
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
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\CashDeposit';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cash_deposit_datatable';
    }
}
