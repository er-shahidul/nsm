<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\Delivery;
use Rbs\Bundle\SalesBundle\Entity\Vehicle;
use Rbs\Bundle\SalesBundle\Form\Type\VehicleDeliverySetForm;
use Rbs\Bundle\SalesBundle\Form\Type\VehicleForm;
use Rbs\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Vehicle Controller.
 *
 */
class VehicleController extends BaseController
{
    /**
     * @Route("/vehicle/list", name="truck_info_list")
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_TRUCK_MANAGE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.vehicle');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Vehicle:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/truck_info_list_ajax", name="truck_info_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_TRUCK_MANAGE")
     */
    public function listAjaxAction()
    {
        $user = $this->getUser();
        $datatable = $this->get('rbs_erp.sales.datatable.vehicle');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb) use ($user)
        {
            $qb->join("sales_vehicles.depo", "d");
            $qb->join("d.users", "u");
            $qb->andWhere("u = :user");
            $qb->setParameter('user', $user);
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/vehicle/my/list", name="truck_info_my_list", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_AGENT")
     */
    public function myIndexAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.my.vehicle');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Vehicle:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/truck_info_my_list_ajax", name="truck_info_my_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_AGENT")
     */
    public function myListAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.my.vehicle');
        $datatable->buildDatatable();
        
        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->join('sales_vehicles.agent', 'a');
            $qb->andWhere('a =:agent');
            $qb->andWhere('sales_vehicles.transportGiven =:AGENT');
            $qb->orderBy('sales_vehicles.createdAt', 'DESC');
            $qb->setParameters(array('agent'=>$this->getAgent(), 'AGENT'=>Vehicle::AGENT));
        };
        $query->addWhereAll($function);
        
        return $query->getResponse();
    }

    /**
     * @Route("/vehicle/in/out/list", name="truck_info_in_out_list", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_IN, ROLE_TRUCK_START, ROLE_TRUCK_FINISH, ROLE_TRUCK_OUT")
     */
    public function inOutIndexAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.in.out.vehicle');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Delivery:vehicle.html.twig', array(
            'datatable' => $datatable
        ));
    }
    
    /**
     * @Route("/truck_info_in_out_list_ajax", name="truck_info_in_out_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_IN, ROLE_TRUCK_START, ROLE_TRUCK_FINISH, ROLE_TRUCK_OUT")
     */
    public function inOutListAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.in.out.vehicle');
        $datatable->buildDatatable();
        $depoId = $this->checkUserDepo();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb) use ($depoId)
        {
            $qb->join('sales_vehicles.depo', 'd');
            $qb->andWhere('sales_vehicles.vehicleOut IS NULL');

            $qb->orderBy('sales_vehicles.createdAt' ,'DESC');

            if($depoId != 0){
                $qb->andWhere('d.id =:depoId');
                $qb->setParameters(array('depoId'=>$depoId));
            }
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/vehicle/load/list", name="vehicle_info_load_list", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_IN, ROLE_TRUCK_START, ROLE_TRUCK_FINISH, ROLE_TRUCK_OUT")
     */
    public function loadIndexAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.load.vehicle');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Delivery:vehicle.html.twig', array(
            'datatable' => $datatable
        ));
    }
    
    /**
     * @Route("/vehicle_info_load_list_ajax", name="vehicle_info_load_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_IN, ROLE_TRUCK_START, ROLE_TRUCK_FINISH, ROLE_TRUCK_OUT")
     */
    public function loadListAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.load.vehicle');
        $datatable->buildDatatable();
        $depoId = $this->checkUserDepo();
        
        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb) use ($depoId)
        {
            if($depoId == 0){
                $qb->join('sales_vehicles.depo', 'd');
                $qb->andWhere('sales_vehicles.vehicleIn IS NOT NULL');
                $qb->andWhere('sales_vehicles.vehicleOut IS NULL');
                $qb->andWhere('sales_vehicles.finishLoad IS NULL');
                $qb->andWhere('sales_vehicles.deliveries IS NOT NULL');
                $qb->orderBy('sales_vehicles.createdAt' ,'DESC');
            }else{
                $qb->join('sales_vehicles.depo', 'd');
                $qb->andWhere('d.id =:depoId');
                $qb->andWhere('sales_vehicles.vehicleIn IS NOT NULL');
                $qb->andWhere('sales_vehicles.vehicleOut IS NULL');
                $qb->andWhere('sales_vehicles.finishLoad IS NULL');
                $qb->andWhere('sales_vehicles.deliveries IS NOT NULL');
                $qb->orderBy('sales_vehicles.createdAt' ,'DESC');
                $qb->setParameters(array('depoId'=>$depoId));
            }
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/vehicle/set/list", name="vehicle_info_set_list", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_IN, ROLE_TRUCK_START, ROLE_TRUCK_FINISH, ROLE_TRUCK_OUT")
     */
    public function setIndexAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.set.vehicle');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Delivery:vehicle.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/vehicle_info_set_list_ajax", name="vehicle_info_set_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_IN, ROLE_TRUCK_START, ROLE_TRUCK_FINISH, ROLE_TRUCK_OUT")
     */
    public function setListAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.set.vehicle');
        $datatable->buildDatatable();
        $depoId = $this->checkUserDepo();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb) use ($depoId)
        {
            if($depoId == 0){
                $qb->andWhere('sales_vehicles.vehicleIn IS NOT NULL');
                $qb->andWhere('sales_vehicles.vehicleOut IS NULL');
                $qb->andWhere('sales_vehicles.deliveries IS NULL');
                $qb->orderBy('sales_vehicles.createdAt' ,'DESC');
            }else{
                $qb->join('sales_vehicles.depo', 'd');
                $qb->andWhere('d.id =:depoId');
                $qb->andWhere('sales_vehicles.vehicleIn IS NOT NULL');
                $qb->andWhere('sales_vehicles.vehicleOut IS NULL');
                $qb->andWhere('sales_vehicles.deliveries IS NULL');
                $qb->orderBy('sales_vehicles.createdAt' ,'DESC');
                $qb->setParameters(array('depoId'=>$depoId));
            }
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }
    
    /**
     * @Route("/vehicle/add", name="truck_info_add")
     * @Template("RbsSalesBundle:Vehicle:form.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_AGENT, ROLE_TRUCK_MANAGE")
     */
    public function addAction(Request $request)
    {
        $vehicle = new Vehicle();
        $form = $this->createForm(new VehicleForm($this->getUser()), $vehicle);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->checkUserForFieldSet($request, $vehicle);
                $this->vehicleRepo()->create($vehicle);
                $this->get('session')->getFlashBag()->add('success', 'Agent Vehicle Info Added Successfully');
                return $this->checkUserTypeForRedirect();
            }
        }

        return array(
            'form' => $form->createView(),
            'user' => $this->getUser()
        );
    }

    /**
     * @Route("/vehicle/in/{id}", name="truck_in", options={"expose"=true})
     * @param Vehicle $vehicle
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_IN")
     */
    public function vehicleInAction(Vehicle $vehicle)
    {
        $vehicle->setVehicleIn(new \DateTime());
        $vehicle->setTransportStatus(Vehicle::IN);
        $this->vehicleRepo()->update($vehicle);
        $this->get('session')->getFlashBag()->add('success', 'Vehicle In Successfully');

//        return $this->redirect($this->generateUrl('truck_info_in_out_list'));
        return $this->redirect($this->generateUrl('deliveries_home'));
    }

    /**
     * @Route("/vehicle/start/{id}", name="truck_start", options={"expose"=true})
     * @param Vehicle $vehicle
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_START")
     */
    public function loadStartAction(Vehicle $vehicle)
    {
        $vehicle->setStartLoad(new \DateTime());
        $vehicle->setTransportStatus(Vehicle::START_LOAD);
        $this->vehicleRepo()->update($vehicle);
        $this->get('session')->getFlashBag()->add('success', 'Vehicle Load Started Successfully');

        return $this->redirect($this->generateUrl('vehicle_info_load_list'));
    }

    /**
     * @Route("/delivery/set/{id}", name="delivery_set", options={"expose"=true})
     * @param Vehicle $vehicle
     * @param Request $request
     * @Template("RbsSalesBundle:Vehicle:vehicle-delivery-set-form.html.twig")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE")
     */
    public function deliverySetAction(Request $request, Vehicle $vehicle)
    {
        $form = $this->createForm(new VehicleDeliverySetForm($this->getUser(), $vehicle->getId()));
        
        if ('POST' === $request->getMethod()) {
            if(!isset($request->request->get('vehicle_delivery_form')['orders'])){
                $this->get('session')->getFlashBag()->add('error', 'Please select at least Order');
                return $this->redirect($this->generateUrl('delivery_set', array('id' => $request->attributes->all()['id'])));
            }
            $delivery = new Delivery();
            $orderText=null;
            foreach ($request->request->get('vehicle_delivery_form')['orders'] as $orderId){
                $order = $this->em()->getRepository('RbsSalesBundle:Order')->find($orderId);
                $delivery->addOrder($order);
                $delivery->setDepo($order->getDepo());
                $orderText =+ $orderId .', ';
            }
            $delivery->setShipped(false);
            $delivery->setTransportGiven(Delivery::NOURISH);
            $this->em()->getRepository('RbsSalesBundle:Delivery')->createDelivery($delivery);

            $vehicle->setTruckInvoiceAttachedBy($this->getUser());
            $vehicle->setTruckInvoiceAttachedAt(new \DateTime());
            $vehicle->setDeliveries($delivery);
            $vehicle->setShipped(false);
            $vehicle->setOrderText($orderText);

            $this->vehicleRepo()->update($vehicle);
            $this->get('session')->getFlashBag()->add('success', 'Vehicle Delivery Set Started Successfully');

            return $this->redirect($this->generateUrl('vehicle_info_set_list'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/vehicle/finish/{id}", name="truck_finish", options={"expose"=true})
     * @param Vehicle $vehicle
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_FINISH")
     */
    public function loadFinishAction(Vehicle $vehicle)
    {
        $vehicle->setFinishLoad(new \DateTime());
        $vehicle->setTransportStatus(Vehicle::FINISH_LOAD);
        $this->vehicleRepo()->update($vehicle);
        $this->get('session')->getFlashBag()->add('success', 'Vehicle Load Finished Successfully');

        return $this->redirect($this->generateUrl('truck_info_in_out_list'));
    }

    /**
     * @Route("/vehicle/out/{id}", name="truck_out", options={"expose"=true})
     * @param Vehicle $vehicle
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE, ROLE_TRUCK_OUT")
     */
    public function truckOutAction(Vehicle $vehicle)
    {
        $vehicle->setVehicleOut(new \DateTime());
        $vehicle->setTransportStatus(Vehicle::OUT);
        $vehicle->setShipped(true);
        $this->vehicleRepo()->update($vehicle);
        $this->get('session')->getFlashBag()->add('success', 'Vehicle Out Successfully');

        return $this->redirect($this->generateUrl('truck_info_in_out_list'));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function checkUserTypeForRedirect()
    {
        return $this->getUser()->getUserType() == User::AGENT ?
            $this->redirect($this->generateUrl('truck_info_my_list')) :
            $this->redirect($this->generateUrl('truck_info_list'));
    }

    /**
     * @param Request $request
     * @param Vehicle $vehicle
     */
    protected function checkUserForFieldSet(Request $request, Vehicle $vehicle)
    {
        if ($this->getUser()->getUserType() == User::AGENT) {
            $order = $this->em()->getRepository('RbsSalesBundle:Order')->find($request->request->get('vehicle')['orders']);
            $vehicle->setAgent($order->getAgent());
            $vehicle->setTransportGiven(Vehicle::AGENT);
            $vehicle->setDepo($order->getDepo());
            $vehicle->setOrderText($request->request->get('vehicle')['orders']);
            $delivery = new Delivery();
            $delivery->addOrder($order);
            $delivery->setShipped(false);
            $delivery->setDepo($order->getDepo());
            $delivery->setTransportGiven(Delivery::AGENT);
            $vehicle->setDeliveries($delivery);
            $this->em()->getRepository('RbsSalesBundle:Delivery')->createDelivery($delivery);
        } else {
            $vehicle->setTransportGiven(Vehicle::NOURISH);
        }
        $vehicle->setShipped(false);
    }

    /**
     * @return int
     */
    protected function checkUserDepo()
    {
        $getDepoId = $this->em()->getRepository('RbsCoreBundle:Depo')
                                ->getDepoId($this->getUser()->getId());
        return $getDepoId ? $depoId = $getDepoId[0]['id'] : $depoId = 0;
    }

    /**
     * @return Agent
     */
    protected function getAgent()
    {
        return $this->em()->getRepository('RbsSalesBundle:Agent')->findOneBy(array(
            'user' => $this->getUser()
        ));
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function em()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return \Rbs\Bundle\SalesBundle\Repository\VehicleRepository
     */
    protected function vehicleRepo()
    {
        return $this->em()->getRepository('RbsSalesBundle:Vehicle');
    }

    /**
     * @Route("/vehicle/{id}", name="vehicle_view", options={"expose"=true})
     * @param Vehicle $vehicle
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function view(Vehicle $vehicle)
    {
        $partialItems = $this->getDoctrine()->getRepository('RbsSalesBundle:DeliveryItem')->getPartialDeliveredItems($vehicle->getDeliveries());
        return $this->render('RbsSalesBundle:Vehicle:view.html.twig', array(
            'vehicle'      => $vehicle,
            'partialItems'  => $partialItems,
        ));
    }

}