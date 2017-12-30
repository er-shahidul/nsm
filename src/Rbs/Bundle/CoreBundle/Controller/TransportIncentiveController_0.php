<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Entity\TransportIncentive;
use Rbs\Bundle\CoreBundle\Form\Type\TransportIncentiveForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Rbs\Bundle\CoreBundle\Entity\Upload;
use Rbs\Bundle\CoreBundle\Form\Type\UploadForm;

/**
 * TransportIncentive Controller.
 *
 */
class TransportIncentiveController extends BaseController
{
    /**
     * @Route("/transport/incentive/list", name="transport_incentive_list")
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TRANSPORT_INCENTIVE_MANAGE")
     */
    public function listAction()
    {
        $transportIncentivesArr = array();
        $transportIncentives = $this->getDoctrine()->getRepository('RbsCoreBundle:TransportIncentive')->getAllTransportIncentive();
        $depos = $this->getDoctrine()->getRepository('RbsCoreBundle:Depo')->getAllActiveDepo();
        $itemTypes = $this->getDoctrine()->getRepository('RbsCoreBundle:ItemType')->getAllActiveItemType();
        $itemTypeCount = $this->getDoctrine()->getRepository('RbsCoreBundle:ItemType')->getItemTypeCount();

        foreach ($transportIncentives as $key => $transportIncentive) {
            $transportIncentivesArr[$transportIncentive['district']][$transportIncentive['station']]['data'][$transportIncentive['depo']][$transportIncentive['itemType']] = $transportIncentive['amount'];
            foreach ($depos as $depo) {
                foreach ($itemTypes as $itemType) {
                    if (!isset($transportIncentivesArr[$transportIncentive['district']][$transportIncentive['station']]['data'][$depo['name']][$itemType['itemType']])) {
                        $transportIncentivesArr[$transportIncentive['district']][$transportIncentive['station']]['data'][$depo['name']][$itemType['itemType']] = "0.0";
                    }
                }
            }
            ksort($transportIncentivesArr[$transportIncentive['district']][$transportIncentive['station']]['data'][$transportIncentive['depo']]);
        }

        return $this->render('RbsCoreBundle:TransportIncentive:index.html.twig', array(
                'transportIncentivesArr' => $transportIncentivesArr,
                'depos' => $depos,
                'itemTypes' => $itemTypes,
                'itemTypeCount' => $itemTypeCount
        ));
    }
    
    /**
     * @Route("/transport/commission/history", name="transport_commission_history", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TRANSPORT_INCENTIVE_MANAGE")
     */
    public function transportCommissionHistoryAction(Request $request)
    {
        $district = $this->getLocationByName($request->query->all()['district']);
        $transportCommissionHistories = $this->getDoctrine()->getRepository('RbsCoreBundle:TransportIncentive')->findBy(
            array('district' => $district->getId(), 'status' => TransportIncentive::ARCHIVED), array('createdAt' => 'DESC'), 10
        );

        return $this->render('RbsCoreBundle:TransportIncentive:history.html.twig', array(
            'transportCommissionHistories' => $transportCommissionHistories,
            'district' => $district
        ));
    }

    /**
     * @Route("/transport/commission/history/{id}", name="transport_commission_history_list", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TRANSPORT_INCENTIVE_MANAGE")
     */
    public function transportCommissionAllHistoryAction(Request $request)
    {
        $district = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->find($request->attributes->get('id'));
            
        $datatable = $this->get('rbs_erp.sales.datatable.transport.commission.history');
        $datatable->buildDatatable();

        return $this->render('RbsCoreBundle:TransportIncentive:historyList.html.twig', array(
            'district' => $district,
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/transport_commission_history_list_ajax/{id}", name="transport_commission_history_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TRANSPORT_INCENTIVE_MANAGE")
     */
    public function transportCommissionAllHistoryAjaxAction(Request $request)
    {
        $district = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->find($request->attributes->get('id'));
        $datatable = $this->get('rbs_erp.sales.datatable.transport.commission.history');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb
         * @param $qb
         */
        $function = function($qb) use ($district)
        {
            $qb->andWhere("core_transport_incentives.status = :ARCHIVED");
            $qb->andWhere("core_transport_incentives.district = :district");
            $qb->setParameter("ARCHIVED", TransportIncentive::ARCHIVED);
            $qb->setParameter("district", $district->getId());
        };
        $query->addWhereAll($function);
        return $query->getResponse();
    }
    
    /**
     * @Route("/transport/incentive/create", name="transport_incentive_create")
     * @Template("RbsCoreBundle:TransportIncentive:form.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_TRANSPORT_INCENTIVE_MANAGE")
     */
    public function createAction(Request $request)
    {
        $transportIncentive = new TransportIncentive();

        $form = $this->createForm(new TransportIncentiveForm(), $transportIncentive, array(
            'action' => $this->generateUrl('transport_incentive_create'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $preTIs = $this->getDoctrine()->getRepository('RbsCoreBundle:TransportIncentive')->getTransportIncentiveForStatusChange($request->request->get('transport_incentive')['level1'], $request->request->get('transport_incentive')['level2'],
                    $request->request->get('transport_incentive')['depo'], $request->request->get('transport_incentive')['itemType']);
                foreach ($preTIs as $preTI){
                    $preTI->setStatus(TransportIncentive::ARCHIVED);
                    $this->getDoctrine()->getRepository('RbsCoreBundle:TransportIncentive')->update($preTI);
                }
                
                $transportIncentive->setDistrict($this->getDoctrine()->getRepository('RbsCoreBundle:Location')->find($request->request->get('transport_incentive')['level1']));
                $transportIncentive->setStation($this->getDoctrine()->getRepository('RbsCoreBundle:Location')->find($request->request->get('transport_incentive')['level2']));
                $transportIncentive->setStatus(TransportIncentive::CURRENT);
                
                $this->getDoctrine()->getRepository('RbsCoreBundle:TransportIncentive')->create($transportIncentive);

                $this->flashMessage('success', 'Transport Incentive Added Successfully');
                return $this->redirect($this->generateUrl('transport_incentive_list'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/transport/incentive/import", name="transport_incentive_import")
     * @Template("RbsCoreBundle:TransportIncentive:import-form.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request)
    {
        $upload = new Upload();
        $form = $this->createForm(new UploadForm(), $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            set_time_limit(0);
            ini_set('memory_limit', '1014M');
            $file = $upload->getFile();
            $fileName = md5(uniqid()).'.csv';
            $file->move(
                $this->getParameter('brochures_directory'),
                $fileName
            );
            $upload->setFile($fileName);

            $loop = 0;
            $locationId = 18713;
            $log = [];
            $file = $this->get('request')->getSchemeAndHttpHost().'/uploads/sales/csv-import/'.$fileName;
            if (($handle = fopen($file, "r")) !== FALSE) {
                fgetcsv($handle);
                while (($data = fgetcsv($handle, 3000, ",")) !== FALSE) {
                    $loop++;
                    $num = count($data);
                    for ($c=0; $c < $num; $c++) {
                        $col[$c] = $data[$c];
                    }
                    $transportIncentiveOlds = $this->getDoctrine()->getRepository('RbsCoreBundle:TransportIncentive')->getTransportIncentiveForStatusChange($this->getLocationByName($col[0]), $this->getLocationByName($col[1]), $this->getDepoByName($col), $this->getItemTypeByName($col));
                    foreach ($transportIncentiveOlds as $transportIncentiveOld){
                        $transportIncentiveOld->setStatus(TransportIncentive::ARCHIVED);
                        $this->getDoctrine()->getRepository('RbsCoreBundle:TransportIncentive')->update($transportIncentiveOld);
                    }
                    $transportIncentive = new TransportIncentive();
                    $transportIncentive->setStatus(TransportIncentive::CURRENT);

                    $district = $this->getLocationByName($col[0]);
                    if (!$district) {
                        $log['D - ' . $col[0]] = '';
                        continue;
                    }
                    $transportIncentive->setDistrict($district);

                    $station = $this->getLocationByName($col[1]);
                    if (!$station) {
                        $dId = $district ? $district->getId() : 0;
                        if (!array_key_exists($col[1], $log)) {
                            $locationId++;
                            $sql = "INSERT INTO core_locations (id, name, parent_id, level) VALUES ($locationId, '{$col[1]}', {$dId}, 5);";
                            $log[$col[1]] = '';
                            echo $sql .PHP_EOL;
                        }

                        $log['S - ' . $col[0] .'('.$dId.') => '. $col[1]] = '';
                        continue;
                    }
                    $transportIncentive->setStation($station);

                    $depo = $this->getDepoByName($col);
                    if (!$depo) {
                        $log['DE - ' . $col[2]] = '';
                        continue;
                    }
                    continue;
                    $transportIncentive->setDepo($depo);
                    $transportIncentive->setItemType($this->getItemTypeByName($col));
                    $transportIncentive->setAmount((float)$col[4]);
                    //$em->persist($transportIncentive);
                    //$em->flush();

                }
                fclose($handle);
            }
            ksort($log);
            var_dump($log);
            exit;

            $this->flashMessage('success', 'Transport Incentive Imported Successfully');
            return $this->redirect($this->generateUrl('transport_incentive_list'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @param $col
     * @return mixed
     */
    protected function getLocationByName($col)
    {
        return $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->findOneByName($col);
    }

    /**
     * @param $col
     * @return mixed
     */
    protected function getDepoByName($col)
    {
        return $this->getDoctrine()->getRepository('RbsCoreBundle:Depo')->findOneByName($col[2]);
    }

    /**
     * @param $col
     * @return mixed
     */
    protected function getItemTypeByName($col)
    {
        return $this->getDoctrine()->getRepository('RbsCoreBundle:ItemType')->findOneByItemType($col[3]);
    }
}