<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

/**
 * Class CashReceiveDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class CashReceiveDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('cash_receive_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('receivedAt', 'datetime', array('title' => 'Date', 'date_format' => 'LLL' ))
            ->add('orderRef.id', 'column', array('title' => 'Order Number'))
            ->add('depo.name', 'column', array('title' => 'Depo'))
            ->add('receivedBy.username', 'column', array('title' => 'Received By'))
            ->add('amount', 'column', array('title' => 'Amount'))
            ->add('depositor', 'column', array('title' => 'Depositor'))
            ->add('remark', 'column', array('title' => 'Remark'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\CashReceive';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cash_receive_datatable';
    }
}
