<?php

namespace Rbs\Bundle\SalesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\SalesBundle\Entity\Incentive;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\OrderItem;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Entity\Sms;

/**
 * OrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderRepository extends EntityRepository
{
    public function getAll()
    {
        return $this->findAll();
    }

    public function create(Order $order)
    {
        $this->calculateOrderAmount($order);
        $this->setStatus($order);
        $this->setSms($order);

        $this->_em->persist($order);
        $this->_em->flush();
    }

    public function update(Order $order, $resetStatus = false)
    {
        $this->calculateOrderAmount($order);
        $this->setSms($order);
        if ($resetStatus) {
            $this->setStatus($order);
        }

        $this->_em->flush();
        return $this->_em;
    }

    public function delete($data)
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }

    public function calculateOrderAmount(Order $order)
    {
        /** @var OrderItem $orderItems */
        foreach ($order->getOrderItems() as $orderItems) {
            $orderItems->setOrder($order);
            $orderItems->calculateTotalAmount(true);
        }
        $order->setTotalAmount($order->getItemsTotalAmount());
    }

    /**
     * @param Order $order
     */
    protected function setStatus(Order $order)
    {
        $order->setOrderState('PROCESSING');
        $order->setPaymentState('PENDING');
        $order->setDeliveryState('PENDING');
    }

    protected function setSms(Order $order)
    {
        if ($order->getRefSMS()) {
            $order->getRefSMS()->setStatus('READ');
            $order->getRefSMS()->setOrder($order);
        }
    }

    function getAgentWiseOrder($agent, $returnQuery = false)
    {
        $query = $this->createQueryBuilder('o')
            ->where('o.deletedAt IS NULL')
            ->orderBy('o.id', 'DESC')
            ->andWhere('o.orderState != :complete or o.orderState != :cancel')
            ->andWhere('o.paymentState != :paid')
            ->andWhere('o.agent = :agent')
            ->setParameter('agent', $agent)
            ->setParameter('complete', Order::ORDER_STATE_COMPLETE)
            ->setParameter('cancel', Order::ORDER_STATE_CANCEL)
            ->setParameter('paid', Order::PAYMENT_STATE_PAID);

        if ($returnQuery) {
            return $query;
        }

        return $query->getQuery()->getResult();
    }

    public function adjustPaymentViaSms($payments)
    {
        /** @var Payment $payment */
        foreach ($payments as $payment) {
            if ($payment->getPaymentVia() == 'SMS') {
                $this->orderAmountAdjust($payment);
            }
        }
    }

    public function orderAmountAdjust($payments)
    {
        /** @var Payment $payments */
        $amount = $payments->getAmount();

        /** @var Order $order */
        foreach($payments->getOrders() as $order){
            $dueAmount = $order->getDueAmount();
            if($amount != 0 and $dueAmount != 0){
                if ($amount >= $dueAmount) {
                    $amount = $amount - $dueAmount;
                    $order->setPaidAmount($order->getPaidAmount() + $dueAmount);
                    $order->setPaymentState(Order::PAYMENT_STATE_PAID);

                    $this->_em->getRepository('RbsSalesBundle:OrderItem')->orderAmountAdjust($order);
                } else{
                    $order->setPaidAmount($order->getPaidAmount() + $amount);
                    $order->setPaymentState(Order::PAYMENT_STATE_PARTIALLY_PAID);
                    $amount = 0;

                    $this->_em->getRepository('RbsSalesBundle:OrderItem')->orderAmountAdjust($order);
                }
            }
            $this->update($order);
        }
    }

    public function updateDeliveryState($orders)
    {
        $orderItemRepo = $this->_em->getRepository('RbsSalesBundle:OrderItem');
        $deliveryItemRepo = $this->_em->getRepository('RbsSalesBundle:DeliveryItem');

        /** @var Order $order */
        foreach ($orders as $order) {

            if ((int)$deliveryItemRepo->getTotalDeliveryItemByOrder($order) < (int)$orderItemRepo->getTotalOrderItemByOrder($order)) {
                $order->setDeliveryState(Order::DELIVERY_STATE_PARTIALLY_SHIPPED);
            } else {
                $order->setDeliveryState(Order::DELIVERY_STATE_SHIPPED);
            }
            $order->setOrderState(Order::ORDER_STATE_COMPLETE);
            $this->_em->persist($order);
            $this->_em->flush();
        }
    }

    public function updateDeliveryStatePartialShipped($orders)
    {
        $orderItemRepo = $this->_em->getRepository('RbsSalesBundle:OrderItem');
        $deliveryItemRepo = $this->_em->getRepository('RbsSalesBundle:DeliveryItem');
        /** @var Order $order */
        foreach ($orders as $orderId) {
            $order = $this->_em->getRepository('RbsSalesBundle:Order')->find($orderId);
            if ((int)$deliveryItemRepo->getTotalDeliveryItemByOrder($order) < (int)$orderItemRepo->getTotalOrderItemByOrder($order)) {
                $order->setDeliveryState(Order::DELIVERY_STATE_PARTIALLY_SHIPPED);
            } else {
                $order->setDeliveryState(Order::DELIVERY_STATE_SHIPPED);
            }

            $order->setOrderState(Order::ORDER_STATE_COMPLETE);
            $this->_em->persist($order);
        }

        $this->_em->flush();
    }

    public function newOrderBySms(Sms $sms)
    {


    }

    public function cancelOrder(Order $order)
    {
        if ($order->getOrderState() != Order::ORDER_STATE_PENDING) {
            $stockRepo = $this->_em->getRepository('RbsSalesBundle:Stock');
            $oldQty = $stockRepo->extractOrderItemQuantity($order);
            $stockRepo->subtractFromOnHold($oldQty, $order->getDepo());
        }

        $order->setOrderState(Order::ORDER_STATE_CANCEL);
        $order->setPaymentState(Order::PAYMENT_STATE_PENDING);
        $order->setDeliveryState(Order::DELIVERY_STATE_PENDING);
        $order->setPaidAmount(0);

        /** @var OrderItem $item */
        foreach ($order->getOrderItems() as $item) {
            $item->setPaidAmount(0);
            $this->_em->persist($item);
        }

        /** @var Payment $payment */
        foreach ($order->getPayments() as $payment) {
            if ($payment->getPaymentVia() == 'SMS' & !$payment->isVerified() && $payment->getTransactionType() == Payment::CR) {
                $this->_em->remove($payment);
            } else if ($payment->getTransactionType() == Payment::DR) {
                $this->_em->remove($payment);
                // Instead of delete DR payment, first though was insert another positive payment
                /*$payment = new Payment();
                $payment->setAgent($payment->getAgent());
                $payment->setAmount($payment->getAmount());
                $payment->setPaymentMethod(Payment::PAYMENT_METHOD_BANK);
                $payment->setRemark('Return due to cancel order: ' . $order->getId());
                $payment->setDepositDate(date("Y-m-d"));
                $payment->setTransactionType(Payment::CR);
                $payment->setVerified(true);
                $payment->addOrder($order);
                $this->_em->getRepository('RbsSalesBundle:Payment')->create($payment);*/
            }
        }

        $this->_em->flush();

        $this->update($order);
    }

    public function getOrdersForSalesIncentive($durationType)
    {
        $query = $this->createQueryBuilder('o');
        $query->join('o.agent', 'a');
        $query->join('o.orderIncentiveFlag', 'oif');
        $query->select('o.id as orderId');
        $query->addSelect('a.id as agentId');

        if($durationType == Incentive::YEAR){
            $query->andWhere('oif.yearFlag = false');
        }else{
            $query->andWhere('oif.monthFlag = false');
        }

        $query->andWhere('o.orderState = :COMPLETE');
        $query->setParameters(array('COMPLETE'=>Order::ORDER_STATE_COMPLETE));

        return $query->getQuery()->getResult();
    }
}