<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Entity\Target;
use Rbs\Bundle\SalesBundle\Form\Type\TargetForm;
use Rbs\Bundle\SalesBundle\Form\Type\TargetUpdateForm;
use Rbs\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Target Controller.
 *
 */
class TargetController extends BaseController
{
    /**
     * @Route("/target/list", name="target_list", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TARGET_MANAGE")
     */
    public function targetListAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.target');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Target:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/target_list_ajax", name="target_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TARGET_MANAGE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.target');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb
         * @param $qb
         */
        $function = function($qb)
        {
            $qb->join('sales_targets.zilla', 'l');
            $qb->andWhere('sales_targets.quantity > 0');
            $qb->andWhere('sales_targets.startDate is not null');
            $qb->andWhere('sales_targets.endDate is not null');
            $qb->orderBy('sales_targets.createdAt', 'desc');
        };
        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * @Route("/target/create", name="target_create", options={"expose"=true})
     * @Template("RbsSalesBundle:Target:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TARGET_MANAGE")
     */
    public function createAction(Request $request)
    {
        $categories = $this->em()->getRepository('RbsCoreBundle:Category')->getAllActiveCategory();
        $target = new Target();
        foreach ($categories as $category) {
            $categoryWiseField = new Target();
            $categoryWiseField->setQuantity(0);
            $categoryWiseField->setCategory($category);
            $targets[] = $categoryWiseField;
            $sc[] = $category->getName();
        }

        $form = $this->createForm(new TargetForm(), $target, array(
            'action' => $this->generateUrl('target_create'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        $form->get('child_entities')->setData($targets);
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->em()->getRepository('RbsSalesBundle:Target')->create($target);
                $this->flashMessage('success', 'Target Added Successfully');
                return $this->redirect($this->generateUrl('target_list'));
            }
        }

        return array(
            'form' => $form->createView(),
            'categories' => $sc
        );
    }

    /**
     * @Route("/target/update/{id}", name="target_update", options={"expose"=true})
     * @Template("RbsSalesBundle:Target:update.html.twig")
     * @param Request $request
     * @param Target $target
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TARGET_MANAGE")
     */
    public function updateAction(Request $request, Target $target)
    {
        $form = $this->createForm(new TargetUpdateForm(), $target);
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em()->getRepository('RbsSalesBundle:Target')->update($target, $request->request->get('target')['endDate']);
                $this->get('session')->getFlashBag()->add('success', 'Target Updated Successfully');
                return $this->redirect($this->generateUrl('target_list'));
            }
        }

        return array(
            'form' => $form->createView(),
            'target' => $target
        );
    }

    /**
     * @Route("/target/my", name="target_my")
     * @Template("RbsSalesBundle:Target:my.html.twig")
     * @return array|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_RSM_GROUP")
     */
    public function myAction()
    {
        $targets = $this->em()->getRepository('RbsSalesBundle:Target')->findMyLocationTargetRSM($this->getUser()->getZilla());
        $srList = $this->em()->getRepository('RbsUserBundle:User')->findSRByParentId($this->getUser()->getId());

        return array(
            'targets'       => $targets,
            'srList'       => $srList,
            'user'       => $this->getUser()
        );
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function em()
    {
        return $this->getDoctrine()->getManager();
    }
}