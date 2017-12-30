<?php

namespace Rbs\Bundle\SalesBundle\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\Delivery;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\OrderIncentiveFlag;
use Rbs\Bundle\SalesBundle\Entity\OrderItem;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Entity\Sms;

class ChickOrderSmsParser
{
    /** @var  EntityManager */
    protected $em;

    /** @var  Sms */
    protected $sms;

    public $error;

    protected $orders = array();

    protected $orderIds = array();

    public function __construct($em)
    {
        $this->em = $em;
    }

    protected function setError($string)
    {
        $this->error = $string;
    }

    protected function hasError()
    {
        return !empty($this->error);
    }

    public function parse(Sms $sms)
    {
        $this->sms = $sms;
        return $this->validateAndExecute();
    }

    public function validateAndExecute()
    {
        $this->em->persist($this->sms);
        $msg = $this->sms->getMsg();
        $splitMsg = array_filter(explode(',', $msg));

        foreach ($splitMsg as $msg) {
            $this->createOrder($msg);
        }

        if (empty($this->orderIds)) {
            $this->sms->setStatus('UNREAD');
            $this->em->flush();
            $this->setError('Invalid Sms Format');

            return null;
        }

        $this->sms->setStatus('READ');
        $this->em->flush();

        return array(
            'orderId' => implode(", ", array_unique($this->orderIds))
        );
    }

    public function markError($string)
    {
        $this->sms->setMsg(str_replace($string, '<span class="error">'.$string.'</span>', $this->sms->getMsg()));
    }

    protected function createOrder($msg)
    {
        if (!strpos($msg, ':')) return false;

        list($agentId, $sku, $qty) = explode(':', $msg);

        $qty = (int)$qty;
        if (!$qty || empty($agentId) || empty($sku)) return false;

        $agent = $this->em->getRepository('RbsSalesBundle:Agent')->findOneBy(array('agentID' => $agentId));
        if (!$agent) return false;

        $item = $this->em->getRepository('RbsCoreBundle:Item')->findOneBy(array('sku' => $sku));
        if (!$item) return false;

        $price = $this->em->getRepository('RbsCoreBundle:ItemPrice')->getCurrentPrice($item, $agent->getUser()->getZilla());
        $orderItem = new OrderItem();
        $orderItem->setItem($item);
        $orderItem->setQuantity($qty);
        $orderItem->setPrice($price);

        $order = array_key_exists($agentId, $this->orders) ? $this->orders[$agentId] : new Order();
        $this->orders[$agentId] = $order;

        $orderItem->setOrder($order);
        $order->addOrderItem($orderItem);
        $order->setAgent($agent);
        $order->setDepo($agent->getDepo());
        $order->setLocation($agent->getUser()->getUpozilla());
        $order->setTotalAmount($order->getItemsTotalAmount());
        $order->setOrderState(Order::ORDER_STATE_PENDING);
        $order->setPaymentState(Order::PAYMENT_STATE_PENDING);
        $order->setDeliveryState(Order::DELIVERY_STATE_PENDING);
        $order->setOrderVia('SMS');
        $order->setRefSMS($this->sms);

        $order->calculateOrderAmount();

        $this->em->persist($orderItem);
        $this->em->persist($order);
        $this->em->flush();
        $this->orderIds[] = $order->getId();

        return true;
    }

    public function trimMobileNo($string)
    {
        return str_replace(array(' ', '+'), '', $string);
    }
}