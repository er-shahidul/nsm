<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Rbs\Bundle\SalesBundle\Entity\CashReceive;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Form\Type\CashReceiveForm;
use Rbs\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Cash Receive Controller.
 *
 */
class CashReceiveController extends BaseController
{
    /**
     * @Route("/cash/receive/list", name="cash_receive_list", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_CASH_RECEIVE_MANAGE")
     */
    public function cashReceiveListAction(Request $request)
    {
        $datatable = $this->get('rbs_erp.sales.datatable.cash.receive');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:CashReceive:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/cash_receive_list_ajax", name="cash_receive_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_CASH_RECEIVE_MANAGE")
     */
    public function listAjaxAction(Request $request)
    {
        $datatable = $this->get('rbs_erp.sales.datatable.cash.receive');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb
         * @param $qb
         */
        $function = function($qb)
        {
            $qb->join('sales_cash_receives.depo', 'd');
            $qb->join('d.users', 'u');
            $qb->andWhere('u.id =:user');
            $qb->setParameter('user', $this->getUser()->getId());
            $qb->orderBy('sales_cash_receives.receivedAt', 'DESC');
        };
        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * @Route("/cash/receive/create", name="cash_receive_create", options={"expose"=true})
     * @Template("RbsSalesBundle:CashReceive:form.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_CASH_RECEIVE_MANAGE")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cashReceive = new CashReceive();
        $payment = new Payment();
        
        $getDepoId = $em->getRepository('RbsCoreBundle:Depo')->getDepoId($this->getUser()->getId());
        $getDepoId ? $depoId = $getDepoId[0]['id'] : $depoId = 0;

        $form = $this->createForm(new CashReceiveForm($depoId), $cashReceive, array(
            'action' => $this->generateUrl('cash_receive_create'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        if($getDepoId && 'POST' === $request->getMethod()){
            $cashReceivedId = $em->getRepository('RbsSalesBundle:CashReceive')->getLastCashReceivedId($this->getUser()->getId());

            if($cashReceivedId!=null && 'POST' === $request->getMethod()){
                $lastTotalReceivedAmount = $em->getRepository('RbsSalesBundle:CashReceive')->lastTotalReceivedAmount($cashReceivedId[0]['id']);
                $lastTotalAmount = $lastTotalReceivedAmount!=null?$lastTotalReceivedAmount[0]:0;
                $total =$lastTotalAmount['totalReceivedAmount']+$_POST['cash_receive']['amount'];
                $cashReceive->setTotalReceivedAmount($total);
            }else if ('POST' === $request->getMethod()){
                $cashReceive->setTotalReceivedAmount($_POST['cash_receive']['amount']);
            }
            if ('POST' === $request->getMethod()) {
                $form->handleRequest($request);

                if ($form->isValid()) {
//                    $od = $em->getRepository('RbsSalesBundle:Order')->find($request->request->get('cash_receive')['orderRef']);
                    $agent = $em->getRepository('RbsSalesBundle:Agent')->find($request->request->get('cash_receive')['agent']);
                    $payment->setAgent($agent);
                    $payment->setDepositedAmount($request->request->get('cash_receive')['amount']);
                    $payment->setAmount($request->request->get('cash_receive')['amount']);
                    $payment->setPaymentMethod(Payment::PAYMENT_METHOD_CASH);
                    $payment->setRemark('Cash received by depo user.');
                    $payment->setDepositDate(date("Y-m-d"));
                    $payment->setTransactionType(Payment::CR);
                    $payment->setVerified(true);
//                    $payment->addOrder($od);
                    $em->getRepository('RbsSalesBundle:Order')->orderAmountAdjust($payment);
                    $em->getRepository('RbsSalesBundle:Payment')->create($payment);

                    $cashReceive->setReceivedAt(new \DateTime());
                    $cashReceive->setReceivedBy($this->getUser());
                    $cashReceive->setDepo($em->getRepository('RbsCoreBundle:Depo')->find($getDepoId[0]['id']));
                    $em->getRepository('RbsSalesBundle:CashReceive')->create($cashReceive);
                    $this->flashMessage('success', 'Cash Received Successfully');
                    return $this->redirect($this->generateUrl('cash_receive_list'));
                }
            }
        }else if ('POST' === $request->getMethod()){
            $this->flashMessage('error', 'You are not a depo user');
        }
        
        return array(
            'form' => $form->createView()
        );
    }
    
    /**
     * @Route("/cash/receive/from/depo/list", name="cash_receive_from_depo_list", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_CASH_RECEIVE_MANAGE")
     */
    public function cashReceiveFromDepoListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cashDepositsData = $em->getRepository('RbsSalesBundle:CashDeposit')->getAllCashDepositGroupByDepo();
        $cashReceives = $em->getRepository('RbsSalesBundle:CashReceive')->getAllCashReceiveGroupByDepo();

        $cashDeposits = array();
        foreach ($cashDepositsData as $value) {
            $cashDeposits[$value['depoId']] = $value;
        }

        return $this->render('RbsSalesBundle:CashReceive:admin.html.twig', array(
            'cashDeposits' => $cashDeposits,
            'cashReceives'=> $cashReceives
        ));
    }
    
    /**
     * @Route("/cash/receive/from/depo/details/{id}", name="cash_receive_from_depo_details", options={"expose"=true})
     * @param Depo $depo
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_CASH_RECEIVE_MANAGE")
     */
    public function cashReceiveFromDepoDetailsListAction(Depo $depo)
    {
        $em = $this->getDoctrine()->getManager();
        $cashDeposits = $em->getRepository('RbsSalesBundle:CashDeposit')->getCashDepositDetails($depo->getId());

        return $this->render('RbsSalesBundle:CashReceive:deposit_details.html.twig', array(
            'cashDeposits' => $cashDeposits,
            'depoName' => $depo->getName()
        ));
    }
    
    /**
     * @Route("/cash/receive/from/depo/receive/details/{id}", name="cash_receive_from_depo_receive_details", options={"expose"=true})
     * @param Depo $depo
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_CASH_RECEIVE_MANAGE")
     */
    public function cashReceiveFromDepoReceiveDetailsListAction(Depo $depo)
    {
        $em = $this->getDoctrine()->getManager();
        $cashReceives = $em->getRepository('RbsSalesBundle:CashReceive')->getCashDepositReceiveDetails($depo->getId());

        return $this->render('RbsSalesBundle:CashReceive:receive_from_agent_details.html.twig', array(
            'cashReceives' => $cashReceives,
            'depoName' => $depo->getName()
        ));
    }
}