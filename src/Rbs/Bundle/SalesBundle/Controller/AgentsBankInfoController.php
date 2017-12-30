<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\AgentsBankInfo;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Form\Type\AgentsBankInfoForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * AgentsBankInfo Controller.
 *
 */
class AgentsBankInfoController extends BaseController
{
    /**
     * @Route("/bank/info/list", name="bank_info_list")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_AGENT, ROLE_BANK_SLIP_VERIFIER, ROLE_BANK_SLIP_APPROVAL")
     */
    public function indexAction()
    {
        $user = $this->getDoctrine()->getManager()->getRepository('RbsSalesBundle:Agent')->findOneBy(array('user' => $this->getUser()));

        if($user){
            $datatable = $this->get('rbs_erp.sales.datatable.agent.bank.info');
        }else{
            $datatable = $this->get('rbs_erp.sales.datatable.agent.bank.info.admin');
        }
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Agent:my_bank_slip_list.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/bank_info_list_ajax", name="bank_info_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_AGENT, ROLE_BANK_SLIP_VERIFIER, ROLE_BANK_SLIP_APPROVAL")
     */
    public function listAjaxAction()
    {
        $user = $this->getDoctrine()->getManager()->getRepository('RbsSalesBundle:Agent')->findOneBy(array('user' => $this->getUser()));
       
        if($user){
            $datatable = $this->get('rbs_erp.sales.datatable.agent.bank.info');
        }else{
            $datatable = $this->get('rbs_erp.sales.datatable.agent.bank.info.admin');
        }
        $datatable->buildDatatable();


        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb) use ($user)
        {
            $qb->join('sales_agents_bank_info.agent', 'u');
            if($user){
                $qb->andWhere('u =:user');
                $qb->setParameter('user', $user);
            }
            $qb->orderBy('sales_agents_bank_info.createdAt', 'DESC');
        };
        $query->addWhereAll($function);
        
        return $query->getResponse();
    }

    /**
     * @Route("/orders/my/bank-info", name="orders_my_bank_info", options={"expose"=true})
     * @Template("RbsSalesBundle:Agent:bank_slip_upload.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_AGENT")
     */
    public function agentBankInfoCreateAction(Request $request)
    {
        $agentsBankInfo = new AgentsBankInfo();
        $form = $this->createForm(new AgentsBankInfoForm($this->getUser()), $agentsBankInfo, array(
            'action' => $this->generateUrl('orders_my_bank_info'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $agent = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->findOneBy(array(
                                                             'user' => $this->getUser()->getId()));
                $agentsBankInfo->setAgent($agent);
                $this->getDoctrine()->getManager()->getRepository('RbsSalesBundle:AgentsBankInfo')->create($agentsBankInfo);
                $this->flashMessage('success', 'Bank info added Successfully');
                return $this->redirect($this->generateUrl('bank_info_list'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/agent/bank/info/cancel/{id}", name="agent_bank_info_cancel", options={"expose"=true})
     * @param AgentsBankInfo $agentsBankInfo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_BANK_SLIP_VERIFIER, ROLE_BANK_SLIP_APPROVAL")
     */
    public function cancelAction(AgentsBankInfo $agentsBankInfo)
    {
        $agentsBankInfo->setStatus(AgentsBankInfo::CANCEL);
        $agentsBankInfo->setCancelAt(new \DateTime());
        $agentsBankInfo->setCancelBy($this->getUser());
        $this->getDoctrine()->getRepository('RbsSalesBundle:AgentsBankInfo')->update($agentsBankInfo);

        $this->get('session')->getFlashBag()->add(
            'success',
            'Agents Bank Info Cancelled Successfully'
        );

        return $this->redirect($this->generateUrl('bank_info_list'));
    }

    /**
     * @Route("/agent/bank/info/verify/{id}", name="agent_bank_info_verify", options={"expose"=true})
     * @param AgentsBankInfo $agentsBankInfo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_BANK_SLIP_VERIFIER")
     */
    public function verifyAction(AgentsBankInfo $agentsBankInfo)
    {
        $agentsBankInfo->setStatus(AgentsBankInfo::VERIFIED);
        $agentsBankInfo->setVerifiedAt(new \DateTime());
        $agentsBankInfo->setVerifiedBy($this->getUser());
        $this->getDoctrine()->getRepository('RbsSalesBundle:AgentsBankInfo')->update($agentsBankInfo);

        $this->get('session')->getFlashBag()->add(
            'success',
            'Agent Bank Info Verified Successfully'
        );

        return $this->redirect($this->generateUrl('bank_info_list'));
    }

    /**
     * @Route("/agent/bank/info/approve/{id}", name="agent_bank_info_approve", options={"expose"=true})
     * @param AgentsBankInfo $agentsBankInfo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_BANK_SLIP_APPROVAL")
     */
    public function approveAction(AgentsBankInfo $agentsBankInfo)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = new Payment();
        $payment->setAgent($agentsBankInfo->getAgent());
        $payment->setAmount($agentsBankInfo->getAmount());
        $payment->setPaymentMethod(Payment::PAYMENT_METHOD_BANK);
        $payment->setRemark('Bank Deposit By Agent.');
        $payment->setBankName($agentsBankInfo->getBankName());
        $payment->setBranchName($agentsBankInfo->getBranchName());
        $payment->setDepositDate(date("Y-m-d"));
        $payment->setTransactionType(Payment::CR);
        $payment->setVerified(true);
        $payment->addOrder($agentsBankInfo->getOrderRef());

        $agentsBankInfo->setStatus(AgentsBankInfo::APPROVED);
        $agentsBankInfo->setApprovedAt(new \DateTime());
        $agentsBankInfo->setApprovedBy($this->getUser());
        $this->getDoctrine()->getRepository('RbsSalesBundle:DamageGood')->update($agentsBankInfo);

        $em->getRepository('RbsSalesBundle:Order')->orderAmountAdjust($payment);
        $em->getRepository('RbsSalesBundle:Payment')->create($payment);

        $this->get('session')->getFlashBag()->add(
            'success',
            'Agents Bank Info Verified Successfully'
        );

        return $this->redirect($this->generateUrl('bank_info_list'));
    }

    /**
     * @Route("/agent/bank/info/view/{id}", name="agent_bank_info_view", options={"expose"=true})
     * @param AgentsBankInfo $agentsBankInfo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_AGENT, ROLE_BANK_SLIP_VERIFIER, ROLE_BANK_SLIP_APPROVAL")
     */
    public function viewAction(AgentsBankInfo $agentsBankInfo)
    {
        return $this->render('RbsSalesBundle:Agent:bank-info-view.html.twig', array(
            'agentsBankInfo' => $agentsBankInfo
        ));
    }

    /**
     * @Route("/uploads/sales/agent-bank-slip/{path}", name="agent_bank_info_doc_view", options={"expose"=true})
     * @return Response
     */
    public function viewDocAction()
    {
        //nothing to have
    }

//    /**
//     * @Route("/agent/bank/info/doc/view/{id}", name="agent_bank_info_doc_view", options={"expose"=true})
//     * @param AgentsBankInfo $agentsBankInfo
//     * @JMS\Secure(roles="ROLE_AGENT, ROLE_BANK_SLIP_VERIFIER, ROLE_BANK_SLIP_APPROVAL")
//     * @return Response
//     */
//    public function viewDocAction(AgentsBankInfo $agentsBankInfo)
//    {
//        $file = WEB_PATH . '/uploads/sales/agent-bank-slip/'.$agentsBankInfo->getPath();
//
//        $response = new Response();
//        $response->headers->set('Content-type', 'application/octet-stream');
//        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', basename($file)));
//        $response->setContent(file_get_contents($file));
//
//        return $response;
//    }
}