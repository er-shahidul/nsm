<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Entity\AgentGroup;
use Rbs\Bundle\SalesBundle\Form\Type\AgentGroupForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Agent Group Controller.
 *
 */
class AgentGroupController extends Controller
{
    /**
     * @Route("/agent/groups", name="agent_groups_home")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.agent.group');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:AgentGroup:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/agent_groups_list_ajax", name="agent_groups_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.agent.group');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->andWhere("sales_agent_groups.deletedAt IS NULL");
        };
        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * @Route("/agent/group/create", name="agent_group_create")
     * @Template("RbsSalesBundle:AgentGroup:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function createAction(Request $request)
    {
        $agentGroup = new AgentGroup();
        $form = $this->createForm(new AgentGroupForm(), $agentGroup);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->em()->getRepository('RbsSalesBundle:AgentGroup')->create($agentGroup);
                $this->get('session')->getFlashBag()->add('success','Agent Group Created Successfully');
                return $this->redirect($this->generateUrl('agent_groups_home'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/agent/group/update/{id}", name="agent_group_update", options={"expose"=true})
     * @Template("RbsSalesBundle:AgentGroup:new.html.twig")
     * @param Request $request
     * @param AgentGroup $agentGroup
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function updateAction(Request $request, AgentGroup $agentGroup)
    {
        $form = $this->createForm(new AgentGroupForm(), $agentGroup);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->em()->getRepository('RbsSalesBundle:AgentGroup')->update($agentGroup);
                $this->get('session')->getFlashBag()->add('success','User Updated Successfully');
                return $this->redirect($this->generateUrl('agent_groups_home'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/agent/group/delete/{id}", name="agent_group_delete", options={"expose"=true})
     * @param AgentGroup $agentGroup
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_AGENT_VIEW, ROLE_AGENT_CREATE")
     */
    public function deleteAction(AgentGroup $agentGroup)
    {
        $this->em()->remove($agentGroup);
        $this->em()->flush();

        $this->get('session')->getFlashBag()->add('success','Agent Group Deleted Successfully');
        return $this->redirect($this->generateUrl('agent_groups_home'));
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function em()
    {
        return $this->getDoctrine()->getManager();
    }
}