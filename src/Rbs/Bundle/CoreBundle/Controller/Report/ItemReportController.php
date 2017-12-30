<?php

namespace Rbs\Bundle\CoreBundle\Controller\Report;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use Rbs\Bundle\CoreBundle\Form\Type\Report\UpozillaWiseItemReportForm;
use Rbs\Bundle\CoreBundle\Form\Type\Report\YearlyItemReportForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\Response;

/**
 * ItemReport controller.
 *
 * @Route("/report")
 */
class ItemReportController extends Controller
{
    /**
     * @Route("/upozilla_wise", name="upozilla_wise_item_report")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
   public function upozillaWiseItemReportAction(Request $request){

       $form = new UpozillaWiseItemReportForm();
       $data = $request->query->get($form->getName());

       $formSearch = $this->createForm($form, $data);

           $itemLists = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->allItems();

           $orderItems = $this->getDoctrine()
                              ->getRepository('RbsSalesBundle:OrderItem')
                              ->getUpozillaWiseItemSalesReport($data,$itemLists);

       return    $this->render('RbsCoreBundle:Report:upozilla-wise-item-report.html.twig', array(
           'formSearch' => $formSearch->createView(),
           'orderItems' => $orderItems,
           'itemLists' => $itemLists
       ));

   }
   /**
     * @Route("/item_yearly_report", name="item_yearly_report")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
   public function itemYearlyReportAction(Request $request){

       $form = new YearlyItemReportForm();
       $data = $request->query->get($form->getName());

       $formSearch = $this->createForm($form, $data);

           $itemLists = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->allItems();

           $yearlyItems = $this->getDoctrine()
                              ->getRepository('RbsSalesBundle:OrderItem')
                              ->getYearlyWiseItemSalesReport($data);
       if($data['year']){

           foreach ($yearlyItems as $data) {
               foreach ($data as $row) {
                    if (!isset($itemLists[$row['itemId']]['total'])) {
                        $itemLists[$row['itemId']]['total'] = 0;
                    }
                   $itemLists[$row['itemId']]['total'] = $itemLists[$row['itemId']]['total'] + $row['quantity'];
               }
           }
        }

       return    $this->render('RbsCoreBundle:Report:yearly-item-report.html.twig', array(
           'formSearch' => $formSearch->createView(),
           'yearlyItems' => $yearlyItems,
           'itemLists' => $itemLists
       ));

   }
   /**
     * @Route("/upozilla_wise_excel", name="upozilla_wise_item_report_excel", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
   public function upozillaWiseItemReportExcelAction(Request $request){

       $form = new UpozillaWiseItemReportForm();
       $data = $request->get($form->getName());

       $orderItems = $this->getDoctrine()
           ->getRepository('RbsSalesBundle:OrderItem')
           ->getUpozillaWiseItemSalesReportExcel($data);
       $itemLists = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->allItems();

       $html =     $this->renderView('RbsCoreBundle:Report:upozilla-wise-item-report-excel.html.twig', array(

           'orderItems' => $orderItems,
           'itemLists' => $itemLists
       ));

       $file="upozillaWiseReport.xls";
       $test="$html";
       header("Content-type: application/vnd.ms-excel");
       header("Content-Disposition: attachment; filename=$file");
       echo $test;die;

   }
   /**
     * @Route("/yearly_item_excel", name="yearly_item_report_excel", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
   public function yearlyItemReportExcelAction(Request $request){

       $form = new YearlyItemReportForm();
       $data = $request->get($form->getName());

       $itemLists = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->allItems();

       $yearlyItems = $this->getDoctrine()
           ->getRepository('RbsSalesBundle:OrderItem')
           ->getYearlyWiseItemSalesReport($data);
       if($data['year']) {
           foreach ($yearlyItems as $data) {
               foreach ($data as $row) {
                   if (!isset($itemLists[$row['itemId']]['total'])) {
                       $itemLists[$row['itemId']]['total'] = 0;
                   }
                   $itemLists[$row['itemId']]['total'] = $itemLists[$row['itemId']]['total'] + $row['quantity'];
               }
           }
       }

       $html =     $this->renderView('RbsCoreBundle:Report:yearly-item-report-excel.html.twig', array(

           'yearlyItems' => $yearlyItems,
           'itemLists' => $itemLists
       ));

       $file="upozillaWiseReport.xls";
       $test="$html";
       header("Content-type: application/vnd.ms-excel");
       header("Content-Disposition: attachment; filename=$file");
       echo $test;die;

   }

    public function excelSheetSet($header_arrays)
    {
        $redArr = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'f5f5f5')
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 11,
                'name'  => 'Calibri'
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $objPHPExcel = new PHPExcel();

        //header set
        foreach($header_arrays as $key => $header_array ){

            $objPHPExcel->getActiveSheet()->setCellValue($key, $header_array);
            $objPHPExcel->getActiveSheet()->getStyle($key)->applyFromArray($redArr);
            $objPHPExcel->getActiveSheet()->getColumnDimension($key[0])->setWidth(22);
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(15);
        }

        return $objPHPExcel;
    }
}
