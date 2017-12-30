<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Form\Search\Type\AgentSearchType;
use Rbs\Bundle\SalesBundle\Form\Search\Type\TwoDateSearchType;
use Rbs\Bundle\SalesBundle\Form\Type\PaymentEditForm;
use Rbs\Bundle\SalesBundle\Form\Type\PaymentForm;
use Rbs\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Payment Controller.
 *
 */
class PaymentController extends BaseController
{
    /**
     * @Route("/payments", name="payments_home", options={"expose"=true})
     * @Template()
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_PAYMENT_VIEW, ROLE_PAYMENT_CREATE, ROLE_PAYMENT_APPROVE, ROLE_PAYMENT_OVER_CREDIT_APPROVE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.payment');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Payment:index.html.twig', array(
            'datatable' => $datatable,
        ));
    }

    /**
     * @Route("/payment_list_ajax", name="payment_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_PAYMENT_VIEW, ROLE_PAYMENT_CREATE, ROLE_PAYMENT_APPROVE, ROLE_PAYMENT_OVER_CREDIT_APPROVE")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAjaxAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $agentRepository = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent');
        $datatable = $this->get('rbs_erp.sales.datatable.payment');
        $datatable->buildDatatable();

        $dateFilter = $request->query->get('columns[0][search][value]', null, true);
        $agentFilter = $request->query->get('columns[1][search][value]', null, true);

        // Reset Date Column search's value to Skip DataTable native search functionality for Date Column
        $columns = $request->query->get('columns');
        $columns[0]['search']['value'] = '';
        $request->query->set('columns', $columns);

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb) use ($dateFilter, $agentFilter, $user, $agentRepository)
        {
            if ($dateFilter) {
                list($fromDate, $toDate) = explode('--', $dateFilter);

                if (!empty($fromDate) && !empty($toDate)) {
                    $qb->andWhere('sales_payments.createdAt BETWEEN :fromDate AND :toDate')
                        ->setParameter('fromDate', date('Y-m-d 00:00:00', strtotime($fromDate)))
                        ->setParameter('toDate', date('Y-m-d 23:59:59', strtotime($toDate)));
                } else if (!empty($fromDate) && empty($toDate)) {
                    $qb->andWhere('sales_payments.createdAt BETWEEN :fromDate AND :toDate')
                        ->setParameter('fromDate', date('Y-m-d 00:00:00', strtotime($fromDate)))
                        ->setParameter('toDate', date('Y-m-d 23:59:59', strtotime($fromDate)));
                }
            }

            if ($agentFilter) {
                $agent = $agentRepository->findOneBy(array('user' => $agentFilter));
                $qb->andWhere('sales_payments.agent = :agent')->setParameter('agent', $agent->getId());
            }

            if ($user->getUserType() == User::AGENT) {
                $agent = $agentRepository->findOneBy(array('user' => $user->getId()));
                $qb->andWhere('sales_payments.agent = :agent')->setParameter('agent', $agent->getId());
            }
            $qb->andWhere('sales_payments.bankAccount IS NOT NULL');
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/payment/create", name="payment_create", options={"expose"=true})
     * @Template("RbsSalesBundle:Payment:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_PAYMENT_CREATE, ROLE_PAYMENT_APPROVE, ROLE_PAYMENT_OVER_CREDIT_APPROVE")
     */
    public function createAction(Request $request)
    {
        $payment = new Payment();
        $form = $this->createForm(new PaymentForm($this->getDoctrine()->getManager(), $this->get('request')), $payment, array(
            'action' => $this->generateUrl('payment_create'),
            'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $payment->setTransactionType(Payment::CR);
                $payment->setVerified(true);
                $em = $this->getDoctrine()->getManager();
                $em->getRepository('RbsSalesBundle:Order')->orderAmountAdjust($payment);
                $em->getRepository('RbsSalesBundle:Payment')->create($payment);

                $this->flashMessage('success', 'Payment Added Successfully');
                return $this->redirect($this->generateUrl('payments_home'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/payment/partial_payment_orders/{id}", name="partial_payment_orders", options={"expose"=true})
     * @param Agent $agent
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_PAYMENT_CREATE, ROLE_PAYMENT_APPROVE, ROLE_PAYMENT_OVER_CREDIT_APPROVE")
     */
    public function getAgentPartialOrder(Agent $agent)
    {
        $orders = $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->getAgentWiseOrder($agent->getId());

        $orderArr = array();
        foreach ($orders as $order) {
            $orderArr[] = array('id' => $order->getId(), 'text' => $order->getOrderIdAndDueAmount());
        }

        return new JsonResponse($orderArr);
    }

    /**
     * @Route("/agents/ledger", name="agents_laser")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_PAYMENT_CREATE, ROLE_PAYMENT_APPROVE, ROLE_PAYMENT_OVER_CREDIT_APPROVE")
     */
    public function laserAction(Request $request)
    {
        $agents = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->getAgentListKeyValue();
        $form = new AgentSearchType($agents);
        $data = $request->query->get($form->getName());
        $formSearch = $this->createForm($form, $data);

        if ($request->query->get('search[start_date]', null, true)){
            $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
            $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
        }

        if (!empty($data['start_date'])) {
            $agentPreviousDebitLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentPreviousDebitLaserTotal($data);
            $agentPreviousCreditLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentPreviousCreditLaserTotal($data);
        } else {
            $agentPreviousDebitLaserTotal = 0;
            $agentPreviousCreditLaserTotal = 0;
        }
        $previousBalance = $agentPreviousCreditLaserTotal - $agentPreviousDebitLaserTotal;

        $agentDebitLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentDebitLaserTotal($data);
        $agentCreditLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentCreditLaserTotal($data);
        $currentBalance = $agentCreditLaserTotal - $agentDebitLaserTotal;

        $agentLaser = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentLaser($data);
        if ($data['agent']) {
            $agent = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->find($data['agent']);
        } else {
            $agent = null;
        }

        return $this->render('RbsSalesBundle:Ledger:ledger.html.twig', array(
            'agentLaser' => $agentLaser,
            'formSearch' => $formSearch->createView(),
            'agentPreviousDebitLaserTotal' => $agentPreviousDebitLaserTotal,
            'agent' => $agent,
            'agentPreviousCreditLaserTotal' => $agentPreviousCreditLaserTotal,
            'previousBalance' => $previousBalance,
            'agentDebitLaserTotal' => $agentDebitLaserTotal,
            'agentCreditLaserTotal' => $agentCreditLaserTotal,
            'currentBalance' => $currentBalance,
        ));
    }

    /**
     * @Route("/my/ledger", name="my_laser")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_AGENT")
     */
    public function myLaserAction(Request $request)
    {
        $userId = $this->getUser()->getId();
        $agent = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->findOneBy(array('user' => $userId));

        $form = new TwoDateSearchType();
        $data = $request->query->get($form->getName());
        $formSearch = $this->createForm($form, $data);

        if ($request->query->get('search[start_date]', null, true)) {
            $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
            $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
        }

        if (!empty($data['start_date'])) {
            $agentPreviousDebitLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getMyPreviousDebitLaserTotal($agent->getId(), $data);
            $agentPreviousCreditLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getMyPreviousCreditLaserTotal($agent->getId(), $data);
        } else {
            $agentPreviousDebitLaserTotal = 0;
            $agentPreviousCreditLaserTotal = 0;
        }
        $previousBalance = $agentPreviousCreditLaserTotal - $agentPreviousDebitLaserTotal;

        $agentDebitLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getMyDebitLaserTotal($agent->getId(), $data);
        $agentCreditLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getMyCreditLaserTotal($agent->getId(), $data);
        $currentBalance = $agentCreditLaserTotal - $agentDebitLaserTotal;

        $agentLaser = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getMyLaser($agent->getId(), $data);

        return $this->render('RbsSalesBundle:Ledger:my-ledger.html.twig', array(
            'agentLaser' => $agentLaser,
            'formSearch' => $formSearch->createView(),
            'agentPreviousDebitLaserTotal' => $agentPreviousDebitLaserTotal,
            'agent' => $agent,
            'agentPreviousCreditLaserTotal' => $agentPreviousCreditLaserTotal,
            'previousBalance' => $previousBalance,
            'agentDebitLaserTotal' => $agentDebitLaserTotal,
            'agentCreditLaserTotal' => $agentCreditLaserTotal,
            'currentBalance' => $currentBalance,
        ));
    }

    /**
     * @Route("/payment_amount_verified/{id}", name="payment_amount_verified", options={"expose"=true})
     * @param Request $request
     * @param Payment $payment
     * @return Response
     */
    public function verifyAction(Request $request, Payment $payment)
    {
        $em = $this->getDoctrine()->getManager();
        $verified = $request->query->get('verified');
        $actualAmount = $request->query->get('actualAmount');
        $depositedAmount = $request->query->get('depositedAmount');

        if ($verified == 'true') {
            $payment->setVerified(true);
            $payment->setAmount($actualAmount);
            $payment->setDepositedAmount($depositedAmount);
            $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->update($payment);
            $data["message"] = 'VERIFIED';
            $data["actualAmount"] = $actualAmount;
        } else {
            $em->remove($payment);
            $em->flush();
            $data["message"] = 'Payment Reject and will deleted shortly';
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/payment/edit/{id}", name="payment_edit")
     * @param Request $request
     * @param Payment $payment
     * @return Response
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")
     */
    public function paymentReviewAction(Request $request, Payment $payment)
    {
        $form = $this->createForm(new PaymentEditForm($this->getDoctrine()->getManager(), $this->get('request'), $payment), $payment, array(
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                if (is_numeric($request->request->get('amount'))) {
                    $payment->setAmount($request->request->get('amount'));
                    $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->update($payment);
                    $this->get('session')->getFlashBag()->add(
                        'success', 'Payment Updated Successfully');

                    return $this->redirect($this->generateUrl('payments_home'));
                }
            }
        }

        return $this->render('RbsSalesBundle:Payment:paymentEdit.html.twig', array(
            'form' => $form->createView(),
            'payment' => $payment,
        ));
    }
}