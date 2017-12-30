<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\AgentBank;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Rbs\Bundle\SalesBundle\Form\Type\AgentBankForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Agent Bank Controller.
 *
 */
class AgentBankController extends BaseController
{
    /**
     * @Route("/agent/banks", name="agent_banks", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")
     */
    public function indexAction()
    {
        $agents = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->agents();
        $datatable = $this->get('rbs_erp.sales.datatable.agent.bank');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Agent:agentBankList.html.twig', array(
            'agents' => $agents,
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/agent_banks_list_ajax", name="agent_banks_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.agent.bank');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->join("sales_agent_banks.agent", "a");
            $qb->join("a.user", "u");
            $qb->join("u.profile", "p");
            $qb->addOrderBy('a.id', 'ASC');
            $qb->addOrderBy('sales_agent_banks.id', 'ASC');
            $qb->andWhere("sales_agent_banks.deletedAt IS NULL");
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/agent/bank/add", name="agent_bank_add")
     * @Template("RbsSalesBundle:Agent:agentBankForm.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")
     */
    public function createAction(Request $request)
    {
        $agentBank = new AgentBank();
        $form = $this->createForm(new AgentBankForm(null), $agentBank);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $agentBanks = $this->getDoctrine()->getRepository('RbsSalesBundle:AgentBank')->getByAgent($request->request->all()['agent_bank'] ["agent"]);
                $agentBank->setCode($this->unique_id($agentBanks));
                $this->getDoctrine()->getRepository('RbsSalesBundle:AgentBank')->create($agentBank);
                $this->get('session')->getFlashBag()->add('success','Agent Bank Successfully!');
                return $this->redirect($this->generateUrl('agent_banks'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    function unique_id($agentBanks) {
        $val=1;
        foreach ($agentBanks as $agentBank){
            $val++;
        }
        return $val;
    }

    /**
     * @Route("/agent/{id}/bank/add", name="agent_bank_individual_add")
     * @Template("RbsSalesBundle:Agent:agentBankForm.html.twig")
     * @param Request $request
     * @param Agent $agent
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")
     */
    public function createIndividualAction(Request $request, Agent $agent)
    {
        $agentBank = new AgentBank();
        $form = $this->createForm(new AgentBankForm($agent), $agentBank);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $agentBanks = $this->getDoctrine()->getRepository('RbsSalesBundle:AgentBank')->getByAgent($agent->getId());
                $agentBank->setCode($this->unique_id($agentBanks, 2));
                $this->getDoctrine()->getRepository('RbsSalesBundle:AgentBank')->create($agentBank);
                $this->get('session')->getFlashBag()->add('success','Agent Bank Successfully!');
                return $this->redirect($this->generateUrl('agent_banks'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/agent/bank/{id}/update", name="agent_bank_update", options={"expose"=true})
     * @Template("RbsSalesBundle:Agent:agentBankForm.html.twig")
     * @param Request $request
     * @param AgentBank $agentBank
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")
     */
    public function updateAction(Request $request, AgentBank $agentBank)
    {
        $form = $this->createForm(new AgentBankForm($agentBank->getAgent()), $agentBank);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getRepository('RbsSalesBundle:AgentBank')->update($agentBank);
                $this->get('session')->getFlashBag()->add('success','Agent Bank Updated Successfully!');
                return $this->redirect($this->generateUrl('agent_banks'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }
}