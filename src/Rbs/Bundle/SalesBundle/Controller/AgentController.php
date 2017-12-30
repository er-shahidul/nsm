<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\AgentDoc;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Form\Type\AgentDocForm;
use Rbs\Bundle\SalesBundle\Form\Type\AgentUpdateForm;
use Rbs\Bundle\SalesBundle\Form\Type\OrderForm;
use Rbs\Bundle\UserBundle\Entity\User;
use Rbs\Bundle\UserBundle\Form\Type\UserUpdatePasswordForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Agent Controller.
 *
 */
class AgentController extends BaseController
{
    /**
     * @Route("/agents", name="agents_home")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function indexAction()
    {
        $agents = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->agents();
        $datatable = $this->get('rbs_erp.sales.datatable.agent');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Agent:index.html.twig', array(
            'agents' => $agents,
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/agents_list_ajax", name="agents_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.agent');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->join("sales_agents.user", "u");
            $qb->andWhere("u.userType =:AGENT");
            $qb->andWhere("sales_agents.deletedAt IS NULL");
            $qb->setParameter("AGENT", User::AGENT);
        };
        $query->addWhereAll($function);

        if ($this->isGranted('ROLE_AGENT')) {
            $query->getQuery()->andWhere('agents.sr = :agent')->setParameter('sr', $this->getUser());
        }

        return $query->getResponse();
    }

    /**
     * @Route("/agent/update/{id}", name="agent_update", options={"expose"=true})
     * @Template("RbsSalesBundle:Agent:update.html.twig")
     * @param Request $request
     * @param Agent $agent
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function updateAction(Request $request, Agent $agent)
    {
        $form = $this->createForm(new AgentUpdateForm($agent->isOpeningBalanceFlag()), $agent, array(
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                if(!$agent->isOpeningBalanceFlag()){
                    $em = $this->getDoctrine()->getManager();
                    $payment = new Payment();

                    $payment->setAgent($agent);
                    $payment->setAmount($agent->getOpeningBalance());
                    $payment->setPaymentMethod(Payment::PAYMENT_METHOD_OPENING_BALANCE);
                    $payment->setRemark('Agents opening balance.');
                    $payment->setDepositDate(date("Y-m-d"));
                    $payment->setTransactionType($agent->getOpeningBalanceType());
                    $payment->setVerified(true);
                    $em->getRepository('RbsSalesBundle:Payment')->create($payment);
                    $agent->setOpeningBalanceFlag(true);
                }

                $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->update($agent);
                $this->get('session')->getFlashBag()->add('success','User Updated Successfully!');
                return $this->redirect($this->generateUrl('agents_home'));
            }
        }

        return array(
            'form' => $form->createView(),
            'agent' => $agent
        );
    }

    /**
     * @Route("/agent/update/password/{id}", name="agent_update_password", options={"expose"=true})
     * @Template("RbsSalesBundle:Agent:update.password.html.twig")
     * @param Request $request
     * @param User $user
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function updatePasswordAction(Request $request, User $user)
    {
        $form = $this->createForm(new UserUpdatePasswordForm(), $user);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user->setPassword($form->get('plainPassword')->getData());
                $user->setPlainPassword($form->get('plainPassword')->getData());

                $this->getDoctrine()->getRepository('RbsUserBundle:User')->update($user);
                $this->get('session')->getFlashBag()->add('notice','Password Successfully Change');
                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/agent/details/{id}", name="agent_details", options={"expose"=true})
     * @Template()
     * @param Agent $agent
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function detailsAction(Agent $agent)
    {
        $this->checkViewDetailAccess($agent);

        $data = array();
        $data['agent'] = $agent->getId();
        $agentBanks = $this->getDoctrine()->getRepository('RbsSalesBundle:AgentBank')->findByAgent($agent);

        $agentDebitLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentDebitLaserTotal($data);
        $agentCreditLaserTotal = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getAgentCreditLaserTotal($data);
        $currentBalance = $agentCreditLaserTotal - $agentDebitLaserTotal;
        
        return $this->render('RbsSalesBundle:Agent:details.html.twig', array(
            'agent' => $agent,
            'agentBanks' => $agentBanks,
            'agentCurrentBalance' => $currentBalance
        ));
    }

    protected function checkViewDetailAccess(Agent $agent)
    {
        if ($this->isGranted('ROLE_AGENT')) {
            if ($agent->getSr() != $this->getUser()) {
                throw new AccessDeniedException('Access Denied');
            }
        }
    }

    /**
     * @Route("/agent/delete/{id}", name="agent_delete", options={"expose"=true})
     * @param Agent $agent
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function deleteAction(Agent $agent)
    {
        if($agent->getUser()->getProfile()->getPath()) {
            $agent->getUser()->getProfile()->removeFile($agent->getUser()->getProfile()->getPath());
        }

        $this->getDoctrine()->getManager()->remove($agent);
        $this->getDoctrine()->getManager()->remove($agent->getUser());
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('success','Agent Successfully Deleted');
        return $this->redirect($this->generateUrl('agents_home'));
    }

    /**
     * @Route("find_agent_ajax", name="find_agent_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @JMS\Secure(roles="ROLE_ORDER_VIEW, ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function findAgentAction(Request $request)
    {
        $agentId = $request->request->get('agent');
        $agentRepo = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent');
        $agent = $agentRepo->find($agentId);

        $order = new Order();
        $order->setAgent($agent);
        $form = $this->createForm(new OrderForm(0), $order);
        $prototype = $this->renderView('@RbsSales/Order/_itemTypePrototype.html.twig', array('form' => $form->createView()));

        return new JsonResponse(array('item_type_prototype' => $prototype));
    }

    /**
     * @Route("/payment/agent-search", name="agent_search", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function getAgents(Request $request)
    {
        $qb = $this->agentRepository()->createQueryBuilder('c');
        $qb->join('c.user', 'u');
        $qb->join('u.profile', 'p');
        $qb->select("u.id, CONCAT(c.agentID, ' - ', p.fullName) AS text");
        $qb->setMaxResults(100);

        if ($q = $request->query->get('q')) {
            $qb->where("c.agentID LIKE '%{$q}%' OR p.fullName LIKE '%{$q}%'");
        }

        return new JsonResponse($qb->getQuery()->getResult());
    }

    /**
     * @Route("/my/doc", name="my_doc")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_AGENT")
     */
    public function myDocAction()
    {
        $agent = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')
            ->findOneBy(array('user' => $this->getUser()->getId()));
        $docs = $this->getDoctrine()->getRepository('RbsSalesBundle:AgentDoc')
            ->getAllFileForLogInAgent($agent->getId());

        return $this->render('RbsSalesBundle:Agent:my-doc.html.twig', array(
            'agent' => $agent,
            'docs' => $docs,
        ));
    }

    /**
     * @Route("/my/doc/add", name="my_doc_add")
     * @Template("RbsSalesBundle:Agent:my-doc-upload.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_AGENT")
     */
    public function agentBankInfoCreateAction(Request $request)
    {
        $agentDoc = new AgentDoc();
        $form = $this->createForm(new AgentDocForm($this->getUser()), $agentDoc, array(
            'action' => $this->generateUrl('my_doc_add'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $agent = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')
                    ->findOneBy(array('user' => $this->getUser()->getId()));
                $agentDoc->setAgent($agent);
                $this->getDoctrine()->getManager()->getRepository('RbsSalesBundle:AgentDoc')->create($agentDoc);
                $this->flashMessage('success', 'Agent Doc add Successfully!');
                return $this->redirect($this->generateUrl('my_doc'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }
}