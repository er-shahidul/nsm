<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\Order;

/**
 * Class DeliveryDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class DeliveryDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures(array_merge($this->defaultFeatures(), array('state_save' => true)));
        $this->options->setOptions(array_merge($this->defaultOptions(), array(
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order' => [[0, 'desc']],
        )));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('delivery_list_ajax'),
            'type' => 'GET'
        ));

        $this->callbacks->setCallbacks(array
            (
                'init_complete' => "function(settings) {
                        Delivery.filterInit();
                }",
                'pre_draw_callback' => "function(settings) {
                    $('.dataTables_scrollHead').find('table thead tr').eq(1).remove();
                }"
            )
        );

        $this->columnBuilder
            ->add('id', 'column', array('title' => 'Delivery Id'))
            ->add('orders.id', 'array', array(
                'title' => 'Orders',
                'data' => 'orders[, ].id'
            ))
            ->add('depo.name', 'column', array('title' => 'Depo'))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'Action',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'delivery_view',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'View',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'edit-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                            'data-target' => "#deliveryView",
                            'data-toggle'=>"modal"
                        ),
                        'role' => 'ROLE_USER',
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
        return 'Rbs\Bundle\SalesBundle\Entity\Delivery';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'delivery_datatable';
    }

    public function generateActionList(Order $order)
    {
        $canEdit = $this->authorizationChecker->isGranted('ROLE_ORDER_EDIT');
        $canView = $this->authorizationChecker->isGranted('ROLE_ORDER_VIEW');
        $canCancel = $this->authorizationChecker->isGranted('ROLE_ORDER_CANCEL');
        $canApproveOrder = $this->authorizationChecker->isGranted('ROLE_ORDER_APPROVE');
        $canApprovePayment = $this->authorizationChecker->isGranted('ROLE_PAYMENT_APPROVE');
        $canApproveOverCredit = $this->authorizationChecker->isGranted('ROLE_PAYMENT_OVER_CREDIT_APPROVE');
        $canOrderVerify = $this->authorizationChecker->isGranted('ROLE_ORDER_VERIFY');

        $html = '<div class="actions">
                <div class="btn-group">
                    <a class="btn btn-sm btn-default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
                    Actions <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                    ';

        if ($canEdit && !in_array($order->getOrderState(), array(Order::ORDER_STATE_COMPLETE, Order::ORDER_STATE_CANCEL))) {
            $html .= $this->generateMenuLink('Edit', 'order_update', array('id' => $order->getId()));
        }

        if ($canView) {
            $html .= $this->generateMenuLink('View', 'order_details', array('id' => $order->getId()));
        }

        if ($canCancel && !in_array($order->getOrderState(), array(Order::ORDER_STATE_COMPLETE, Order::ORDER_STATE_CANCEL))) {
            $html .= $this->generateMenuLink('Cancel', 'order_cancel', array('id' => $order->getId()));
        }

        if ($canApproveOrder && in_array($order->getOrderState(), array(Order::ORDER_STATE_PENDING))) {
            //$html .= $this->generateMenuLink('Approve Order', 'order_approve', array('id' => $order->getId()));
            $html .= '<li><a href="'.$this->router->generate('order_summery_view', array('id'=>$order->getId())).'" rel="tooltip" title="show-action" class="" role="button" data-target="#ajaxSummeryView" data-toggle="modal"><i class="glyphicon"></i> Approve Order</a></li>';
        }

        if ($canApprovePayment && in_array($order->getOrderState(), array(Order::ORDER_STATE_PROCESSING)) && in_array($order->getPaymentState(), array(Order::PAYMENT_STATE_PENDING))) {
            $html .= '<li><a href="'.$this->router->generate('review_payment', array('id'=>$order->getId())).'" rel="tooltip" title="show-action" class="" role="button" data-target="#ajaxSummeryView" data-toggle="modal"><i class="glyphicon"></i> Approve Payment</a></li>';
        }

        if ($canApproveOverCredit && in_array($order->getPaymentState(), array(Order::PAYMENT_STATE_CREDIT_APPROVAL))) {
            $html .= $this->generateMenuLink('Approve Credit', 'order_approve', array('id' => $order->getId()));
        }

        if ($canOrderVerify && $order->getOrderState() == Order::ORDER_STATE_PROCESSING
            && in_array($order->getPaymentState(), array(Order::PAYMENT_STATE_PAID, Order::PAYMENT_STATE_PARTIALLY_PAID))
            && in_array($order->getDeliveryState(), array(Order::DELIVERY_STATE_PENDING))
        ) {
            $html .= '<li><a href="'.$this->router->generate('order_review', array('id'=>$order->getId())).'" rel="tooltip" title="show-action" class="" role="button" data-target="#ajaxSummeryView" data-toggle="modal"><i class="glyphicon"></i> Verify Order</a></li>';
        }
            $html .='</ul>
                </div>
            </div>';

        return $html;
    }

    protected function generateMenuLink($label, $route = null, $param = array())
    {
        $link = !$route ? 'javascript:;' : $this->router->generate($route, $param);

        return sprintf('<li><a href="%s"> <i class="i"></i> %s </a> </li>', $link, $label);
    }
}