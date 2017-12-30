<?php

namespace Rbs\Bundle\SalesBundle\Controller\Report;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Form\Search\Type\DistrictItemMonthSearchType;
use Rbs\Bundle\SalesBundle\Form\Search\Type\SearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Report Controller.
 *
 */
class ReportController extends Controller
{
    /**
     * @Route("/reports", name="reports_home")
     * @Template()
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALES_REPORT")
     */
    public function indexAction()
    {
        return $this->render('RbsSalesBundle:Report:index.html.twig', array(

        ));
    }

    /**
     * @Route("/report/item", name="report_item")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALES_REPORT")
     */
    public function orderItemReportAction(Request $request)
    {
        $form = new SearchType();
        $data = $request->get($form->getName());
        $form = $this->createForm($form);
        $form->submit($data);

        $items = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->allItems();
        $orderItems = $this->getDoctrine()->getRepository('RbsSalesBundle:OrderItem')->orderItemReport($_POST);

        foreach($items as $id => $row) {
            if(isset($orderItems[$id])) {
                $orderItemsReport[$id] = $orderItems[$id];
            }else{
                $orderItemsReport[$id]['id'] = $row['id'];
                $orderItemsReport[$id]['itemName'] = $row['name'];
                $orderItemsReport[$id]['quantity'] = 0;
                $orderItemsReport[$id]['totalAmount'] = 0;
            }
        }

        return $this->render('RbsSalesBundle:Report:itemReport.html.twig', array(
            'orderItems' => $orderItemsReport,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/report/district-wise-item-monthly", name="district_wise_item_monthly_report")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALES_REPORT")
     */
    public function districtWiseItemMonthlyReportAction(Request $request)
    {
        $form = new DistrictItemMonthSearchType();
        $data = $request->query->get($form->getName());
        $formSearch = $this->createForm($form, $data);

        if(!empty($data['year']) or !empty($data['month'])){
            $orderItems = $this->getDoctrine()->getRepository('RbsSalesBundle:OrderItem')->getCompleteOrderItemByMonth($data);
            $zillas = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->getZillaByParentId($data);
            $items = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->getItemName($data);
        }else{
            $orderItems = null;
            $zillas = null;
            $items = null;
        }

        return $this->render('RbsSalesBundle:Report:district-wise-item-monthly-report.html.twig', array(
            'formSearch' => $formSearch->createView(),
            'orderItems' => $orderItems
        ));
    }
}