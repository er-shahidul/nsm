<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Entity\ItemType;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\OrderIncentiveFlag;
use Rbs\Bundle\SalesBundle\Entity\OrderItem;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Form\Type\OrderForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Order Controller.
 *
 */
class OrderController extends BaseController
{
    /**
     * @Route("/orders", name="orders_home")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_DEPO_USER, ROLE_ORDER_VIEW, ROLE_ORDER_CREATE, ROLE_ORDER_EDIT, ROLE_ORDER_APPROVE, ROLE_ORDER_CANCEL")
     */
    public function indexAction()
    {
        if($this->isGranted('ROLE_DEPO_USER') and !$this->isGranted('ROLE_ADMIN')){
            $datatable = $this->get('rbs_erp.sales.datatable.order.depo');
        }else{
            $datatable = $this->get('rbs_erp.sales.datatable.order');
        }
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Order:index.html.twig', array(
            'datatable' => $datatable,
        ));
    }

    /**
     * @Route("/orders_list_ajax", name="orders_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_DEPO_USER, ROLE_ORDER_VIEW, ROLE_ORDER_CREATE, ROLE_ORDER_EDIT, ROLE_ORDER_APPROVE, ROLE_ORDER_CANCEL")
     */
    public function listAjaxAction()
    {
        if($this->isGranted('ROLE_DEPO_USER') and !$this->isGranted('ROLE_ADMIN')){
            $datatable = $this->get('rbs_erp.sales.datatable.order.depo');
        }else{
            $datatable = $this->get('rbs_erp.sales.datatable.order');
        }
        $datatable->buildDatatable();
        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            if($this->isGranted('ROLE_DEPO_USER')){
                $qb->join('sales_orders.depo', 'd');
                $qb->join('d.users', 'u');
                $qb->andWhere('u.id = :user');
                $qb->setParameter('user', $this->getUser()->getId());
            }
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/orders/my", name="orders_my_home")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_AGENT")
     */
    public function myOrdersAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.order.individual');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Order:my.html.twig', array(
            'datatable' => $datatable,
        ));
    }
    /**
     * @Route("/orders_list_my_ajax", name="orders_list_my_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_AGENT")
     */
    public function listAjaxMyAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.order.individual');
        $datatable->buildDatatable();
        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->join('sales_orders.agent', 'a');
            $qb->join('a.user', 'u');
            $qb->andWhere('u.id =:user');
            $qb->setParameter('user', $this->getUser()->getId());
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/order/readable/sms", name="order_readable_sms")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_VIEW, ROLE_ORDER_CREATE, ROLE_ORDER_EDIT, ROLE_ORDER_APPROVE, ROLE_ORDER_CANCEL")
     */
    public function indexOrderReadableSmsAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.order.readable.sms');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Order:readable.html.twig', array(
            'datatable' => $datatable,
        ));
    }
    
    /**
     * @Route("/order_readable_sms_list_ajax", name="order_readable_sms_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_ORDER_VIEW, ROLE_ORDER_CREATE, ROLE_ORDER_EDIT, ROLE_ORDER_APPROVE, ROLE_ORDER_CANCEL")
     */
    public function listAjaxOrderReadableSmsAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.order.readable.sms');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->join('sales_orders.refSMS', 's');
            $qb->andWhere('s is not null');
            $qb->orderBy('sales_orders.createdAt', 'DESC');
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/order/create", name="order_create", options={"expose"=true})
     * @Template("RbsSalesBundle:Order:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_CREATE, ROLE_ORDER_EDIT, ROLE_ORDER_APPROVE")
     */
    public function createAction(Request $request)
    {
        $order = new Order();
        $orderIncentiveFlag = new OrderIncentiveFlag();

        if ($request->query->get('sms')) {
            $refSms = $request->query->get('sms');
        } else {
            $refSms = 0;
        }

        $form = $this->createForm(new OrderForm($refSms), $order);
        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            /** @var OrderItem $item */
            foreach ($order->getOrderItems() as $item){
                if ($item->getQuantity() < 1) {
                    $this->flashMessage('error', 'Invalid Item Quantity');
                    goto a;
                }

                $price = $this->getDoctrine()->getRepository('RbsCoreBundle:ItemPrice')->getCurrentPrice(
                    $item->getItem(), $order->getDepo()->getLocation()
                );
                if ($price < 1) {
                    $this->flashMessage('error', 'Invalid Item Price');
                    goto a;
                }
            }

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $order->setLocation($order->getAgent()->getUser()->getUpozilla());
                $orderIncentiveFlag->setOrder($order);
                $this->orderRepository()->create($order);
                $this->getDoctrine()->getRepository('RbsSalesBundle:OrderIncentiveFlag')->create($orderIncentiveFlag);
                $depo = $this->getDoctrine()->getRepository('RbsCoreBundle:Depo')->find($request->request->get('order')['depo']);
                $em->getRepository('RbsSalesBundle:Stock')->addStockToOnHold($order, $depo);
                
                $this->flashMessage('success', 'Order Created Successfully');

                $smsSender = $this->get('rbs_erp.sales.service.smssender');
                $smsSender->agentBankInfoSmsAction("Your Order No:".$order->getId().".", $order->getAgent()->getUser()->getProfile()->getCellphone());

                return $this->redirect($this->generateUrl('orders_home'));
            }
            a:
            return $this->redirect($this->generateUrl('order_create'));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/order/update/{id}", name="order_update", options={"expose"=true})
     * @Template("RbsSalesBundle:Order:edit.html.twig")
     * @param Request $request
     * @param Order $order
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_EDIT, ROLE_ORDER_APPROVE, ROLE_ORDER_CANCEL")
     */
    public function updateAction(Request $request, Order $order)
    {
        if ($request->query->get('sms')) {
            $refSms = $request->query->get('sms');
        } else {
            $refSms = 0;
        }

        if (in_array($order->getOrderState(), array(ORDER::ORDER_STATE_CANCEL, ORDER::ORDER_STATE_COMPLETE))
            || in_array($order->getDeliveryState(), array(ORDER::DELIVERY_STATE_READY))) {
            $this->flashMessage('error', 'Invalid Operation');
            return $this->redirectToRoute('orders_home');
        }

        $form = $this->createForm(new OrderForm($refSms), $order);
        $em = $this->getDoctrine()->getManager();

        $depoAttr = Order::ORDER_STATE_PROCESSING == $order->getOrderState() ? array('disabled'=>'disabled') : array();
        if ('POST' === $request->getMethod()) {
            $sms = $order->getRefSMS();
            $stockRepo = $em->getRepository('RbsSalesBundle:Stock');
            $prevDepo = clone $order->getDepo();
            $prevOrderItems = $stockRepo->extractOrderItemQuantity($order);
            $form->handleRequest($request);

            $depo = $order->getDepo() ? $order->getDepo() : $prevDepo;

            /** @var OrderItem $item */
            foreach ($order->getOrderItems() as $item){
                if ($item->getQuantity() < 1) {
                    $this->flashMessage('error', 'Invalid Item Quantity');
                    goto a;
                }

                /*$price = $this->getDoctrine()->getRepository('RbsCoreBundle:ItemPrice')->getCurrentPrice(
                    $item->getItem(), $depo->getLocation()
                );*/
                if ($item->getPrice() < 1) {
                    $this->flashMessage('error', 'Invalid Item Price');
                    goto a;
                }
            }

            if ($form->isValid()) {
                if ($sms) {
                    $em->getRepository('RbsSalesBundle:Sms')->removeOrder($sms);
                }

                $stockRepo->updateStock($order, $depo, $prevOrderItems);
                $em->getRepository('RbsSalesBundle:Order')->update($order, true);

                $this->flashMessage('success', 'Order Updated Successfully');

                return $this->redirect($this->generateUrl('orders_home'));
            }
            a:
            return $this->redirect($this->generateUrl('order_update', array('id' => $order->getId())));
        }

        return array(
            'form' => $form->createView(),
            'order' => $order,
            'depoAttr' => $depoAttr,
        );
    }

    /**
     * @Route("/order/details/{id}", name="order_details", options={"expose"=true})
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_VIEW, ROLE_ORDER_CREATE, ROLE_ORDER_EDIT, ROLE_ORDER_APPROVE, ROLE_ORDER_CANCEL")
     */
    public function detailsAction(Order $order)
    {
        $this->checkViewOrderAccess($order);
        $deliveryItems = $this->getDoctrine()->getRepository('RbsSalesBundle:DeliveryItem')->findBy(array(
            'order' => $order->getId(),
        ));

        $deliveredItems = $this->getDoctrine()->getRepository('RbsSalesBundle:DeliveryItem')->getDeliveredItems($order);
        $auditLogs = $this->getDoctrine()->getRepository('RbsCoreBundle:AuditLog')->getByTypeOrObjectId(array(
            'order.approved', 'order.verified', 'order.hold', 'order.canceled', 'payment.approved', 'payment.over.credit.approved'), $order->getId());

        return $this->render('RbsSalesBundle:Order:details.html.twig', array(
            'order' => $order,
            'deliveryItems' => $deliveryItems,
            'deliveredItems' => $deliveredItems,
            'auditLogs' => $auditLogs,
        ));
    }

    protected function checkViewOrderAccess(Order $order)
    {
        if ($this->isGranted('ROLE_AGENT') && $order->getAgent()->getUser()->getId() != $this->getUser()->getId()) {
            throw new AccessDeniedException('Access Denied');
        }

        if ($this->isGranted('ROLE_AGENT')) {
            $isOwnAgent = $this->agentRepository()->findOneBy(array(
                'agent' => $this->getUser(),
                'id' => $order->getAgent()->getId(),
            ));
            if (!$isOwnAgent) {
                throw new AccessDeniedException('Access Denied');
            }
        }
    }

    /**
     * @Route("/order/approve/{id}", name="order_approve", options={"expose"=true})
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_APPROVE")
     */
    public function orderApproveAction(Order $order)
    {
        if (!$this->isOrderValidState($order)) {
            return $this->redirectOnInvalidOrderState($order);
        }

        if ($order->getOrderState() == Order::ORDER_STATE_PENDING) {
            $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->addStockToOnHold($order, $order->getDepo());
        }

        $order->setOrderState(Order::ORDER_STATE_PROCESSING);
        $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->update($order);

        $this->dispatchApproveProcessEvent('order.approved', $order);
        $this->flashMessage('success', 'Order Approved Successfully');
        return $this->redirect($this->generateUrl('orders_home'));
    }

    /**
     * @Route("/order/cancel/{id}", name="order_cancel", options={"expose"=true})
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_APPROVE, ROLE_ORDER_CANCEL, ROLE_ORDER_EDIT")
     */
    public function orderCancelAction(Order $order)
    {
        if (!$this->isOrderValidState($order)) {
            return $this->redirectOnInvalidOrderState($order);
        }

        $this->orderRepository()->cancelOrder($order);
        $this->dispatchApproveProcessEvent('order.canceled', $order);
        $this->flashMessage('success', 'Order Cancelled Successfully');
        return $this->redirect($this->generateUrl('orders_home'));
    }

    /**
     * @Route("/order/hold/{id}", name="order_hold", options={"expose"=true})
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_APPROVE")
     */
    public function orderHoldAction(Order $order)
    {
        if (!$this->isOrderValidState($order)) {
            return $this->redirectOnInvalidOrderState($order);
        }

        if ($order->getOrderState() == Order::ORDER_STATE_PENDING) {
            $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->addStockToOnHold($order, $order->getAgent()->getDepo());
        }

        $order->setOrderState(Order::ORDER_STATE_HOLD);
        $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->update($order);
        $this->dispatchApproveProcessEvent('order.hold', $order);
        $this->flashMessage('success', 'Order Holded Successfully');
        return $this->redirect($this->generateUrl('orders_home'));
    }

    /**
     * @Route("/order/summery/view/{id}", name="order_summery_view", options={"expose"=true})
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_VIEW, ROLE_ORDER_CREATE, ROLE_ORDER_EDIT, ROLE_ORDER_APPROVE, ROLE_ORDER_CANCEL")
     */
    public function summeryViewAction(Order $order)
    {
        $chickenCheck = 0;
        $chickenCheckForAgent = null;
        $availableCheck = false;
        $orderInfoValid = true;
        $invalidMessage = '';
        $stockRepo = $this->getDoctrine()->getRepository('RbsSalesBundle:Stock');
        /** @var OrderItem $item */
        foreach ($order->getOrderItems() as $item) {
            $stockItem = $stockRepo->findOneBy(
                array('item' => $item->getItem()->getId(), 'depo' => $order->getDepo()->getId())
            );
            $orderInfoValid = $item->isAvailable = $stockItem && $stockItem->isStockAvailable($item->getQuantity());
            if ($item->getItem()->getItemType() == ItemType::Chick) {
                $chickenCheckForAgent = $this->getDoctrine()->getRepository('RbsSalesBundle:ChickenSetForAgent')->findOneBy(
                    array(
                        'item' => $item->getItem()->getId(),
                        'agent' => $order->getAgent()->getId(),
                    )
                );
                if ($chickenCheckForAgent) {
                    $availableCheck = $chickenCheckForAgent->isStockAvailable($item->getQuantity());
                    $orderInfoValid = $item->isAvailableQty = $chickenCheckForAgent->getQuantity();
                } else {
                    $availableCheck = false;
                    $orderInfoValid = $item->isAvailableQty = 0;
                }
                $chickenCheck = 1;
            }

            if ($item->getPrice() < 1) {
                $orderInfoValid = false;
                $invalidMessage = 'Invalid Item Price';
            }
        }

        return $this->render('RbsSalesBundle:Order:summeryView.html.twig', array(
            'order' => $order,
            'chickenCheck' => $chickenCheck,
            'chickenCheckForAgent' => $chickenCheckForAgent,
            'availableCheck' => $availableCheck,
            'orderInfoValid' => $orderInfoValid,
            'invalidMessage' => $invalidMessage,
        ));
    }

    protected function isOrderValidState(Order $order)
    {
        if (in_array($order->getOrderState(), array(
            Order::ORDER_STATE_CANCEL,
            Order::ORDER_STATE_COMPLETE,
        ))) {
            return false;
        }

        return true;
    }

    protected function redirectOnInvalidOrderState(Order $order)
    {
        $this->flashMessage('error', 'Order ' . $order->getId() . ' state is '. $order->getOrderState());

        return $this->redirect($this->generateUrl('orders_home'));
    }

    /**
     * @Route("/order/{id}/payment-review", name="review_payment")
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_PAYMENT_APPROVE")
     */
    public function paymentReviewAction(Order $order)
    {
        $creditLimitRepo = $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit');
        $categoryWiseCreditSummary = $creditLimitRepo->getCategoryWiseCreditLimit($order);
        $orderItemCategoryTotal = $order->categorySum();
        $isOverCredit = $creditLimitRepo->isOverCreditLimitInAnyCategory($orderItemCategoryTotal, $categoryWiseCreditSummary);
        $payments = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getPaymentsBy(array(
            'orders' => array($order->getId()),
            'transactionType' => 'CR',
        ));

        return $this->render('RbsSalesBundle:Order:paymentReview.html.twig', array(
            'order' => $order,
            'payments' => $payments,
            'creditSummary' => $categoryWiseCreditSummary,
            'isOverCredit' => $isOverCredit,
            'orderItemCategoryTotal' => $orderItemCategoryTotal,
        ));
    }

    /**
     * @Route("/order/{id}/order-review", name="order_review")
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_VERIFY")
     */
    public function orderReviewAction(Order $order)
    {
        $auditLogs = $this->getDoctrine()->getRepository('RbsCoreBundle:AuditLog')->getByTypeOrObjectId(array('order.approved', 'order.hold', 'payment.approved', 'payment.over.credit.approved'), $order->getId());
        $data['agent']= $order->getAgent()->getId();
        $data['end_date']= date('Y-m-d');
        $agentDebitLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentDebitLaserTotal($data);
        $agentCreditLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentCreditLaserTotal($data);
        $currentBalance = $agentDebitLaserTotal - ($agentCreditLaserTotal - $order->getTotalPaymentActualAmount());
        return $this->render('RbsSalesBundle:Order:orderVerify.html.twig', array(
            'order' => $order,
            'auditLogs' => $auditLogs,
            'outStandingBalance'=>$currentBalance
        ));
    }

    /**
     * @Route("/order/{id}/approve-payment", name="approve_payment")
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_PAYMENT_APPROVE")
     */
    public function paymentApproveAction(Order $order)
    {
        $agent = $order->getAgent();
        $creditLimitRepo = $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit');
        $categoryWiseCreditSummary = $creditLimitRepo->getCategoryWiseCreditLimit($order);
        $isOverCredit = $creditLimitRepo->isOverCreditLimitInAnyCategory($order, $categoryWiseCreditSummary);

        if (!$agent->isVIP() && $isOverCredit) {
            $order->setPaymentState(Order::PAYMENT_STATE_CREDIT_APPROVAL);
        } else if ($order->getTotalAmount() === $order->getPaidAmount()) {
            $order->setPaymentState(Order::PAYMENT_STATE_PAID);
            $this->orderRepository()->adjustPaymentViaSms($order->getPayments());
        } else {
            $order->setPaymentState(Order::PAYMENT_STATE_PARTIALLY_PAID);
            $this->orderRepository()->adjustPaymentViaSms($order->getPayments());
        }

        /*$em = $this->getDoctrine()->getManager();
        $payment = new Payment();
        $payment->setAgent($order->getAgent());
        $payment->setAmount($order->getTotalAmount());
        $payment->setPaymentMethod(Payment::PAYMENT_METHOD_BANK);
        $payment->setRemark('Order: ' . $order->getId());
        $payment->setDepositDate(date("Y-m-d"));
        $payment->setTransactionType(Payment::DR);
        $payment->setVerified(true);
        $payment->addOrder($order);
        $em->getRepository('RbsSalesBundle:Payment')->create($payment);

        $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->update($order);*/

        $this->dispatchApproveProcessEvent('payment.approved', $order);
        $this->flashMessage('success', 'Payment Approved Successfully');
        return $this->redirect($this->generateUrl('orders_home'));
    }

    /**
     * @Route("/order/{id}/approve-credit-payment", name="approve_credit_payment")
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_PAYMENT_OVER_CREDIT_APPROVE")
     */
    public function paymentOverCreditApproveAction(Order $order)
    {
        /** TODO: Refactor adjustment method. Multiple Update Query Executed */
        $this->orderRepository()->adjustPaymentViaSms($order->getPayments());

        if ($order->getTotalAmount() === $order->getPaidAmount()) {
            $order->setPaymentState(Order::PAYMENT_STATE_PAID);
        } else {
            $order->setPaymentState(Order::PAYMENT_STATE_PARTIALLY_PAID);
        }

        /*$em = $this->getDoctrine()->getManager();
        $payment = $em->getRepository('RbsSalesBundle:Payment')->findByOrdersVerifiedType($order->getId(),Payment::DR, true);

        if($payment == null){
            $payment = new Payment();
            $payment->setAgent($order->getAgent());
            $payment->setAmount($order->getTotalAmount());
            $payment->setPaymentMethod(Payment::PAYMENT_METHOD_BANK);
            $payment->setRemark('Order: ' . $order->getId());
            $payment->setDepositDate(date("Y-m-d"));
            $payment->setTransactionType(Payment::DR);
            $payment->setVerified(true);
            $payment->addOrder($order);
            $em->getRepository('RbsSalesBundle:Payment')->create($payment);
        }*/

        $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->update($order);
        $this->dispatchApproveProcessEvent('payment.over.credit.approved', $order);
        $this->flashMessage('success', 'Payment Approved Successfully');
        return $this->redirect($this->generateUrl('orders_home'));
    }

    /**
     * @Route("/order/{id}/verify", name="order_verify")
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ORDER_VERIFY")
     */
    public function orderVerifyAction(Order $order)
    {
        if ($order->getOrderState() == Order::ORDER_STATE_PROCESSING &&
            in_array($order->getPaymentState(), array(Order::PAYMENT_STATE_PAID, Order::PAYMENT_STATE_PARTIALLY_PAID))
        ) {
            $order->setDeliveryState(Order::DELIVERY_STATE_READY);
            $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->update($order);
            $this->dispatchApproveProcessEvent('order.verified', $order);
            $this->flashMessage('success', 'Order Verified Successfully and Ready for Delivery');
        }

        return $this->redirect($this->generateUrl('orders_home'));
    }

    /**
     * @Route("/order/order-search", name="order_search", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function getCompleteOrderList(Request $request)
    {
        $location = $this->getUser()->getZilla();
        $qb = $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->createQueryBuilder('o')
            ->select('o.id, d.name as depo, p.fullName as agentName, o.createdAt')
            ->join('o.agent', 'a')
            ->join('a.user', 'u')
            ->join('u.profile', 'p')
            ->join('o.depo', 'd')
            ->where('o.orderState = :COMPLETE')
            ->andWhere('u.zilla = :location')
            ->orderBy('o.id', 'DESC')
            ->setParameter('location', $location)
            ->setParameter('COMPLETE', Order::ORDER_STATE_COMPLETE);
        $qb->setMaxResults(10);
        if ($orderId = $request->query->get('q')) {
            $qb->andWhere("o.id LIKE '%{$orderId}%'");
        }

        $output = [];
        foreach ($qb->getQuery()->getResult() as $row) {
            $output[] = [
                'id' => $row['id'],
                'text' => 'Order Id: '.$row['id'].', Depo: '.$row['depo']. ', Agent: ' . $row['agentName'] . ', Date: ' . $row['createdAt']->format('d-m-Y')
            ];
        }

        return new JsonResponse($output);
    }
}