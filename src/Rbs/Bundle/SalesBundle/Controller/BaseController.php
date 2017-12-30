<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Rbs\Bundle\CoreBundle\Controller\BaseController as CoreBaseController;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Event\OrderApproveEvent;

/**
 * Base Controller.
 *
 */
class BaseController extends CoreBaseController
{
    /**
     * @return \Rbs\Bundle\SalesBundle\Repository\AgentRepository
     */
    protected function agentRepository()
    {
        return $this->getDoctrine()->getRepository('RbsSalesBundle:Agent');
    }

    /**
     * @return \Rbs\Bundle\SalesBundle\Repository\OrderRepository
     */
    protected function orderRepository()
    {
        return $this->getDoctrine()->getRepository('RbsSalesBundle:Order');
    }

    /**
     * @return \Rbs\Bundle\SalesBundle\Repository\PaymentRepository
     */
    protected function paymentRepository()
    {
        return $this->getDoctrine()->getRepository('RbsSalesBundle:Payment');
    }

    /**
     * @return \Rbs\Bundle\SalesBundle\Repository\OrderItemRepository
     */
    protected function orderItemRepository()
    {
        return $this->getDoctrine()->getRepository('RbsSalesBundle:OrderItem');
    }

    /**
     * @return \Rbs\Bundle\SalesBundle\Repository\DeliveryRepository
     */
    protected function deliveryRepository()
    {
        return $this->getDoctrine()->getRepository('RbsSalesBundle:Delivery');
    }

    protected function dispatchApproveProcessEvent($tag, Order $order)
    {
        $this->dispatch($tag, new OrderApproveEvent($order, $this->get('request')));
    }
}