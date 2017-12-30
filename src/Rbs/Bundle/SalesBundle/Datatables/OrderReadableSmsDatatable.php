<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

/**
 * Class OrderReadableSmsDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class OrderReadableSmsDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('order_readable_sms_list_ajax'),
            'type' => 'GET'
        ));
        
        $this->columnBuilder->add('id', 'column', array('title' => 'Order No'))
            ->add('createdAt', 'datetime', array('title' => 'Date', 'date_format' => $dateFormat))
            ->add('orderState', 'column', array('title' => 'Order State', 'render' => 'Order.OrderStateFormat'))
            ->add('paymentState', 'column', array('title' => 'Payment State', 'render' => 'Order.OrderStateFormat'))
            ->add('deliveryState', 'column', array('title' => 'Delivery State', 'render' => 'Order.OrderStateFormat'))
            ->add('totalAmount', 'column', array('title' => 'Total Amount', 'render' => 'Order.OrderPaymentFormat'))
            ->add('paidAmount', 'column', array('title' => 'Paid Amount', 'render' => 'Order.OrderPaymentFormat'))
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
        return 'order_datatable';
    }
}
