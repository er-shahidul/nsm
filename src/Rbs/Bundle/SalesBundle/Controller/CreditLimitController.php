<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Entity\CreditLimit;
use Rbs\Bundle\SalesBundle\Form\Type\CreditLimitForm;
use Rbs\Bundle\SalesBundle\Form\Type\CreditLimitWithAgentForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Credit Limit Controller.
 *
 */
class CreditLimitController extends BaseController
{
    /**
     * @Route("/credit/limit/list", name="credit_limit_list", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_CREDIT_LIMIT_MANAGE")
     */
    public function creditLimitListAction(Request $request)
    {
        $datatable = $this->get('rbs_erp.sales.datatable.credit.limit');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:CreditLimit:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/credit_limit_list_ajax", name="credit_limit_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_CREDIT_LIMIT_MANAGE")
     */
    public function listAjaxAction(Request $request)
    {
        $datatable = $this->get('rbs_erp.sales.datatable.credit.limit');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb
         * @param $qb
         */
        $function = function($qb)
        {
            $qb->join('sales_credit_limits.agent', 'a');
            $qb->join('a.user', 'u');
            $qb->join('u.profile', 'p');
            $qb->andWhere('sales_credit_limits.amount > 0');
            $qb->orderBy('sales_credit_limits.createdAt', 'desc');
            $qb->orderBy('sales_credit_limits.status', 'asc');
        };
        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * @Route("/credit/limit/create", name="credit_limit_create")
     * @Template("RbsSalesBundle:CreditLimit:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_CREDIT_LIMIT_MANAGE")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('RbsCoreBundle:Category')->getAllActiveCategory();

        $creditLimit = new CreditLimit();

        foreach ($categories as $category) {
            $categoryWiseField = new CreditLimit();
            $categoryWiseField->setCategory($category);
            $creditLimits[] = $categoryWiseField;
            $sc[] = $category->getName();
            $ca[] = $category->getId();
        }

        $form = $this->createForm(new CreditLimitForm(), $creditLimit, array(
            'action' => $this->generateUrl('credit_limit_create'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        $form->get('child_entities')->setData($creditLimits);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $i = 0;
                foreach ($request->request->get('credit_limit')['child_entities'] as $entity){
                    $creditLimitPrevious = $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->getAllPreviousCreditLimit(
                        $request->request->get('credit_limit')['agent'], $ca[$i]);

                    foreach ($creditLimitPrevious as $clPrevious){
                        $clPrevious->setStatus(CreditLimit::INACTIVE);
                        $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->update($clPrevious);
                    }
                    $creditLimit = new CreditLimit();
                    $creditLimit->setCategory($this->getDoctrine()->getRepository('RbsCoreBundle:Category')->find($ca[$i]));
                    $creditLimit->setCreatedAt(new \DateTime());
                    $creditLimit->setCreatedBy($this->getUser());
                    $creditLimit->setAmount($entity['amount']);
                    $creditLimit->setAgent($this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->find($request->request->get('credit_limit')['agent']));

                    $creditLimit->setStartDate(new DateTime($entity['startDate']));
                    $creditLimit->setEndDate(new DateTime($entity['endDate']));
                    $i = $i + 1;
                    $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->create($creditLimit);
                }

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Credit Limit Add Successfully!'
                );

                return $this->redirect($this->generateUrl('credit_limit_list'));
            }
        }

        return array(
            'form' => $form->createView(),
            'categories' => $sc,
        );
    }

    /**
     * @Route("/credit/limit/notification/list", name="credit_limit_notification_list")
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_CREDIT_LIMIT_MANAGE")
     */
    public function creditLimitNotificationListAction()
    {
        $creditLimitNotifications = $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->creditLimitNotifications();

        return $this->render('RbsSalesBundle:CreditLimit:notification.html.twig', array(
            'creditLimitNotifications' => $creditLimitNotifications
        ));
    }

    /**
     * @Route("/credit/limit/add/{agentId}/{categoryId}/{creditLimitId}", name="credit_limit_add")
     * @Template("RbsSalesBundle:CreditLimit:new_form.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_CREDIT_LIMIT_MANAGE")
     */
    public function addAction(Request $request)
    {
        $agentId = $request->attributes->get('agentId');
        $categoryId = $request->attributes->get('categoryId');
        $creditLimitId = $request->attributes->get('creditLimitId');
        $creditLimit = new CreditLimit();

        $form = $this->createForm(new CreditLimitWithAgentForm($agentId, $categoryId), $creditLimit);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $creditLimit->setCreatedAt(new \DateTime());
                $creditLimit->setCreatedBy($this->getUser());

                $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->create($creditLimit);
                
                $oldCreditLimit = $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->find($creditLimitId);
                $oldCreditLimit->setStatus(CreditLimit::INACTIVE);
                $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->update($oldCreditLimit);
                
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Credit Limit Add Successfully!'
                );

                return $this->redirect($this->generateUrl('credit_limit_list'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    protected function checkCreditLimit($agentId, $startDate, $endDate)
    {
        $agent = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->find($agentId);
        $agentOpeningBalance = $agent->getOpeningBalance();
        $agentCreditLimit = $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->checkCreditLimit($agentId, $startDate, $endDate);
        $agentOrderItem = $this->getDoctrine()->getRepository('RbsSalesBundle:OrderItem')->getAmountDiff($agentId, $startDate, $endDate);
        $crCategoryWise = array();
        $cr = array();
        foreach ($agentCreditLimit as $value) {
            if (!isset($cr[(int)$value['categoryId']])) {
                $cr[(int)$value['categoryId']] = array('creditLimitAmount' => 0, 'paidAmount' => 0, 'totalAmount' => 0);
            }
            $cr[(int)$value['categoryId']]['agentId'] = $value['agentId'];
            $cr[(int)$value['categoryId']]['agentName'] = $value['agentName'];
            $cr[(int)$value['categoryId']]['categoryName'] = $value['categoryName'];
            $cr[(int)$value['categoryId']]['categoryId'] = $value['categoryId'];
            $cr[(int)$value['categoryId']]['endDate'] = $value['endDate'];
            $cr[(int)$value['categoryId']]['startDate'] = $value['startDate'];
            $cr[(int)$value['categoryId']]['creditLimitAmount'] = (float)$value['amount'];
        }
        foreach ($agentOrderItem as $value) {
            $cr[(int)$value['categoryId']]['paidAmount'] = (float)$value['paidAmount'];
            $cr[(int)$value['categoryId']]['totalAmount'] = (float)$value['totalAmount'];
        }

        foreach ($cr as $value) {
            $crCategoryWise[(int)$value['categoryId']]['agentName'] = $value['agentName'];
            $crCategoryWise[(int)$value['categoryId']]['categoryName'] = $value['categoryName'];
            $crCategoryWise[(int)$value['categoryId']]['categoryId'] = $value['categoryId'];
            $crCategoryWise[(int)$value['categoryId']]['creditLimit'] = (float)$value['creditLimitAmount'] + $agentOpeningBalance + (float)$value['paidAmount'] - (float)$value['totalAmount'];
        }

        return $crCategoryWise;
    }
}