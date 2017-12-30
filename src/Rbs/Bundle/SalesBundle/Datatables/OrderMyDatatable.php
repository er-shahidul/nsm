<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

/**
 * Class OrderMyDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class OrderMyDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions(array_merge($this->defaultOptions(), array('order' => [[0, 'desc']])));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('orders_list_my_ajax'),
            'type' => 'GET'
        ));

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->columnBuilder
            ->add('id', 'column', array('title' => 'Order ID'))
            ->add('createdAt', 'datetime', array('title' => 'Date', 'date_format' => $dateFormat))
            ->add('orderState', 'column', array('title' => 'Order State', 'render' => 'Order.OrderStateFormat'))
            ->add('paymentState', 'column', array('title' => 'Payment State', 'render' => 'Order.OrderStateFormat'))
            ->add('deliveryState', 'column', array('title' => 'Delivery State', 'render' => 'Order.OrderStateFormat'))
            ->add('totalAmount', 'column', array('title' => 'Total Amount', 'render' => 'Order.OrderPaymentFormat'))
            ->add('paidAmount', 'column', array('title' => 'Paid Amount', 'render' => 'Order.OrderPaymentFormat'))
            ->add('isComplete', 'virtual', array('visible' => false))
            ->add('isCancel', 'virtual', array('visible' => false))
            ->add('enabled', 'virtual', array('visible' => false))
            ->add('disabled', 'virtual', array('visible' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\Order';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_my_datatable';
    }
}
