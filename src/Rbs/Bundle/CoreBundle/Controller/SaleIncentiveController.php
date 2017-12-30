<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Entity\SaleIncentive;
use Rbs\Bundle\CoreBundle\Entity\Upload;
use Rbs\Bundle\CoreBundle\Form\Type\SaleIncentiveForm;
use Rbs\Bundle\CoreBundle\Form\Type\UploadForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * SaleIncentive Controller.
 *
 */
class SaleIncentiveController extends BaseController
{
    /**
     * @Route("/sale/incentive/list", name="sale_incentive_list")
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALE_INCENTIVE_MANAGE")
     */
    public function listAction()
    {
        /** Disable ONLY_FULL_GROUP_BY for mysql 5.6 or higher */
        $this->getDoctrine()->getConnection()->exec("SET sql_mode = ''");

        $groupMonth = array();
        $groupYear = array();

        $saleIncentivesForMonthGroup = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->getAllMonthIncentiveByMonthGroup();
        $totalMonthGroupName = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->getTotalMonthGroupName();
        foreach ($totalMonthGroupName as $key => $groupName) {
            foreach ($saleIncentivesForMonthGroup as $saleIncentive) {
                if($saleIncentive['group'] == $groupName['group']){
                    $groupMonth[$key][$saleIncentive['name']] = $saleIncentive['name'];
                }
            }
        }

        $saleIncentivesForMonthGroup = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->getAllMonthGroupIncentive();
        $saleIncentiveA = array();
        foreach ($saleIncentivesForMonthGroup as $i => $saleIncentive) {
            $saleIncentiveA[$saleIncentive['group']][] = $saleIncentive;
        }

        $saleIncentivesForYearGroup = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->getAllYearGroupIncentive();
        $saleIncentiveB = array();
        foreach ($saleIncentivesForYearGroup as $i => $saleIncentive) {
            $saleIncentiveB[$saleIncentive['group']][] = $saleIncentive;
        }

        $saleIncentivesForYearGroup = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->getAllYearIncentiveByMonthGroup();
        $totalYearGroupName = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->getTotalYearGroupName();
        foreach ($totalYearGroupName as $key => $groupName) {
            foreach ($saleIncentivesForYearGroup as $saleIncentive) {
                if($saleIncentive['group'] == $groupName['group']){
                    $groupYear[$key][$saleIncentive['name']] = $saleIncentive['name'];
                }
            }
        }

        return $this->render('RbsCoreBundle:SaleIncentive:index.html.twig', array(
            'groupMonth' => $groupMonth,
            'groupYear' => $groupYear,
            'saleIncentiveA' => $saleIncentiveA,
            'saleIncentiveB' => $saleIncentiveB,
            'labelTon' => SaleIncentive::LABEL_TON,
            'labelPerKg' => SaleIncentive::LABEL_PER_KG
        ));
    }

    /**
     * @Route("/sale/incentive/monthly/history", name="sale_incentive_monthly_history", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALE_INCENTIVE_MANAGE")
     */
    public function saleIncentiveMonthlyHistoryAction()
    {
        $salesIncentiveHistories = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->findBy(
            array('status' => SaleIncentive::ARCHIVED, 'durationType' => SaleIncentive::MONTH), array('createdAt' => 'DESC'), 20
        );

        return $this->render('RbsCoreBundle:SaleIncentive:history.html.twig', array(
            'salesIncentiveHistories' => $salesIncentiveHistories,
            'group' => 'Monthly',
            'labelTon' => SaleIncentive::LABEL_TON,
            'labelPerKg' => SaleIncentive::LABEL_PER_KG
        ));
    }
    
    /**
     * @Route("/sale/incentive/monthly/history/list", name="sale_incentive_monthly_history_list", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALE_INCENTIVE_MANAGE")
     */
    public function saleIncentiveMonthlyAllHistoryAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.sale.incentive.monthly.history');
        $datatable->buildDatatable();

        return $this->render('RbsCoreBundle:SaleIncentive:historyList.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/sale_incentive_monthly_history_list_ajax", name="sale_incentive_monthly_history_list_ajax", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALE_INCENTIVE_MANAGE")
     */
    public function saleIncentiveMonthlyAllHistoryAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.sale.incentive.monthly.history');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb
         * @param $qb
         */
        $function = function($qb)
        {
            $qb->andWhere("core_sale_incentives.status = :ARCHIVED");
            $qb->andWhere("core_sale_incentives.durationType = :MONTHLY");
            $qb->setParameter("ARCHIVED", SaleIncentive::ARCHIVED);
            $qb->setParameter("MONTHLY", SaleIncentive::MONTH);
        };
        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * @Route("/sale/incentive/yearly/history", name="sale_incentive_yearly_history", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALE_INCENTIVE_MANAGE")
     */
    public function saleIncentiveYearlyHistoryAction()
    {
        $salesIncentiveHistories = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->findBy(
            array('status' => SaleIncentive::ARCHIVED, 'durationType' => SaleIncentive::YEAR), array('createdAt' => 'DESC'), 20
        );

        return $this->render('RbsCoreBundle:SaleIncentive:history-yearly.html.twig', array(
            'salesIncentiveHistories' => $salesIncentiveHistories,
            'group' => 'Yearly'
        ));
    }
    
    /**
     * @Route("/sale/incentive/yearly/history/list", name="sale_incentive_yearly_history_list", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALE_INCENTIVE_MANAGE")
     */
    public function saleIncentiveYearlyAllHistoryAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.sale.incentive.yearly.history');
        $datatable->buildDatatable();

        return $this->render('RbsCoreBundle:SaleIncentive:historyList-yearly.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/sale_incentive_yearly_history_list_ajax", name="sale_incentive_yearly_history_list_ajax", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALE_INCENTIVE_MANAGE")
     */
    public function saleIncentiveYearlyAllHistoryAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.sale.incentive.yearly.history');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb
         * @param $qb
         */
        $function = function($qb)
        {
            $qb->andWhere("core_sale_incentives.status = :ARCHIVED");
            $qb->andWhere("core_sale_incentives.durationType = :MONTHLY");
            $qb->setParameter("ARCHIVED", SaleIncentive::ARCHIVED);
            $qb->setParameter("MONTHLY", SaleIncentive::YEAR);
        };
        $query->addWhereAll($function);
        return $query->getResponse();
    }
    
    /**
     * @Route("/sale/incentive/create", name="sale_incentive_create")
     * @Template("RbsCoreBundle:SaleIncentive:form.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALE_INCENTIVE_MANAGE")
     */
    public function createAction(Request $request)
    {
        $saleIncentive = new SaleIncentive();

        $form = $this->createForm(new SaleIncentiveForm(), $saleIncentive, array(
            'action' => $this->generateUrl('sale_incentive_create'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                foreach($request->request->get('sale_incentive')['category'] as $value){

                    $preSIs = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->getSalesIncentiveForStatusChange($value, $request->request->get('sale_incentive')['quantity'],
                        $request->request->get('sale_incentive')['durationType'], $request->request->get('sale_incentive')['group']);
                    foreach ($preSIs as $preSI){
                        $preSI->setStatus(SaleIncentive::ARCHIVED);
                        $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->update($preSI);
                    }
                    
                    $saleIncentive = new SaleIncentive();
                    $saleIncentive->setAmount($request->request->get('sale_incentive')['amount']);
                    $saleIncentive->setCategory($this->getDoctrine()->getRepository('RbsCoreBundle:Category')->find($value));
                    $saleIncentive->setQuantity($request->request->get('sale_incentive')['quantity']);
                    $saleIncentive->setDurationType($request->request->get('sale_incentive')['durationType']);
                    $saleIncentive->setGroup($request->request->get('sale_incentive')['group']);
                    $saleIncentive->setType(SaleIncentive::SALE);
                    $saleIncentive->setStatus(SaleIncentive::CURRENT);
                    $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->create($saleIncentive);
                }

                $this->flashMessage('success', 'Sale Incentive Added Successfully');
                return $this->redirect($this->generateUrl('sale_incentive_list'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/sale/incentive/import", name="sale_incentive_import")
     * @Template("RbsCoreBundle:SaleIncentive:import-form.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request)
    {
        $upload = new Upload();
        $form = $this->createForm(new UploadForm(), $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $upload->getFile();
            $fileName = md5(uniqid()).'.csv';
            $file->move(
                $this->getParameter('brochures_directory'),
                $fileName
            );
            $upload->setFile($fileName);

            $file = $this->get('request')->getSchemeAndHttpHost().'/uploads/sales/csv-import/'.$fileName;
            if (($handle = fopen($file, "r")) !== FALSE) {
                fgetcsv($handle);
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    for ($c=0; $c < $num; $c++) {
                        $col[$c] = $data[$c];
                    }
                    $saleIncentiveOlds = $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->getSalesIncentiveForStatusChange($this->getCategoryByName($col[0]), $col[1], $col[2], $col[3]);
                    foreach ($saleIncentiveOlds as $saleIncentiveOld){
                        $saleIncentiveOld->setStatus(SaleIncentive::ARCHIVED);
                        $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->update($saleIncentiveOld);
                    }
                    $saleIncentive = new SaleIncentive();
                    $saleIncentive->setStatus(SaleIncentive::CURRENT);
                    $saleIncentive->setCategory($this->getCategoryByName($col[0]));
                    $saleIncentive->setQuantity($col[1]);
                    $saleIncentive->setDurationType($col[2]);
                    $saleIncentive->setGroup($col[3]);
                    $saleIncentive->setAmount($col[4]);
                    $saleIncentive->setType(SaleIncentive::SALE);
                    $this->getDoctrine()->getRepository('RbsCoreBundle:SaleIncentive')->create($saleIncentive);
                }
                fclose($handle);
            }

            $this->flashMessage('success', 'Sales Incentive Imported Successfully');
            return $this->redirect($this->generateUrl('sale_incentive_list'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @param $col
     * @return mixed
     */
    protected function getCategoryByName($col)
    {
        return $this->getDoctrine()->getRepository('RbsCoreBundle:Category')->findOneByName($col);
    }
}