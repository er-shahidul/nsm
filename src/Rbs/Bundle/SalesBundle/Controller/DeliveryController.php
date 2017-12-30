<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Rbs\Bundle\SalesBundle\Entity\Delivery;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Event\DeliveryEvent;
use Rbs\Bundle\SalesBundle\Form\Type\DeliveryAddForm;
use Rbs\Bundle\SalesBundle\Form\Type\DeliveryForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Delivery Controller.
 *
 */
class DeliveryController extends BaseController
{
    /**
     * @Route("/challan/add", name="deliveries_home")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE")
     */
    public function indexAction(Request $request)
    {
        $datatable = $this->get('rbs_erp.sales.datatable.delivery');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:Delivery:index.html.twig', array(
            'datatable' => $datatable,
        ));
    }

    /**
     * @Route("/delivery_list_ajax", name="delivery_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE")
     * @param Request $request
     * @return Response
     */
    public function listAjaxAction(Request $request)
    {
        $datatable = $this->get('rbs_erp.sales.datatable.delivery');
        $datatable->buildDatatable();

        $dateFilter = $request->query->get('columns[1][search][value]', null, true);
        $orderFilter = $request->query->get('columns')[0]['search']['value'];

        $columns = $request->query->get('columns');
        $columns[0]['search']['value'] = '';
        $columns[1]['search']['value'] = '';
        $request->query->set('columns', $columns);

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        /** @var QueryBuilder $qb */
        $function = function($qb) use ($dateFilter, $orderFilter)
        {
            $qb->join('sales_deliveries.depo', 'd');
            $qb->join('sales_deliveries.orders', 'o');
            $qb->join('d.users', 'u');
            $qb->andWhere('u =:user');
            $qb->andWhere('sales_deliveries.shipped = 0');
            $qb->andWhere('orders.deliveryState IN (:READY) OR orders.deliveryState IN (:PARTIALLY_SHIPPED)');
            $qb->setParameters(array('user'=>$this->getUser(), 'READY'=>Order::DELIVERY_STATE_READY, 'PARTIALLY_SHIPPED'=>Order::DELIVERY_STATE_PARTIALLY_SHIPPED));

            if ($orderFilter) {
                $qb->andWhere('o.id =:orders');
                $qb->setParameter('orders', $orderFilter);
            }
            if ($dateFilter) {
                $qb->andWhere('orders.createdAt BETWEEN :fromDate AND :toDate')
                    ->setParameter('fromDate', date('Y-m-d 00:00:00', strtotime($dateFilter)))
                    ->setParameter('toDate', date('Y-m-d 23:59:59', strtotime($dateFilter)));
            }
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/delivery/{id}", name="delivery_view", options={"expose"=true})
     * @param Delivery $delivery
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE")
     */
    public function view(Delivery $delivery)
    {
        $partialItems = $this->getDoctrine()->getRepository('RbsSalesBundle:DeliveryItem')->getPartialDeliveredItems($delivery);
        return $this->render('RbsSalesBundle:Delivery:view.html.twig', array(
            'delivery'      => $delivery,
            'partialItems'  => $partialItems,
        ));
    }

    /**
     * @Route("/update-delivery/{id}", name="update_delivery", options={"expose"=true})
     * @Template("RbsSalesBundle:Delivery:_edit.html.twig")
     * @param Request $request
     * @param Delivery $delivery
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_ORDER_EDIT")
     */
    public function updateDeliveryAction(Request $request, Delivery $delivery)
    {
        $form = $this->createForm(
            new DeliveryForm(), $delivery, array(
                'action' => $this->generateUrl('update_delivery', array('id' => $delivery->getId())),
                'attr'   => array(
                    'novalidate' => 'novalidate',
                ),
            )
        );

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($delivery);
                $em->flush();

                $this->flashMessage('success', 'Delivery Information Update Successfully');

                return $this->redirect($this->generateUrl('order_details', array('id' => $delivery->getOrders()->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }
    
    /**
     * @Route("/delivery-save/{id}", name="delivery_save", options={"expose"=true})
     * @param Request $request
     * @param Delivery $delivery
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE")
     */
    public function deliverySetAction(Request $request, Delivery $delivery)
    {
        $data = $this->getDoctrine()->getRepository('RbsSalesBundle:Delivery')->save($delivery, $this->get('request')->request->all());
        $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->updateDeliveryState($data['orders']);
        $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->removeStock($delivery);
        $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->createDeliveredProductValue($delivery);

        if (!empty($this->get('request')->request->get('checked-vehicles'))) {
            foreach ($this->get('request')->request->get('checked-vehicles') as $vehicleId => $vehicle) {
                $vehicleObj = $this->getDoctrine()->getRepository('RbsSalesBundle:Vehicle')->find($vehicleId);
                $vehicleObj->setShipped(true);
                $this->getDoctrine()->getRepository('RbsSalesBundle:Vehicle')->update($vehicleObj);
            }
        }

        $this->dispatch('delivery.delivered', new DeliveryEvent($delivery));

        $this->flashMessage('success', 'Delivery Completed Successfully');

        return $this->redirect($this->generateUrl('vehicle_info_load_list'));
    }

    /**
     * @Route("/delivery/vehicle/add", name="delivery_add", options={"expose"=true})
     * @Template("RbsSalesBundle:Delivery:delivery-add.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DELIVERY_MANAGE")
     */
    public function addDeliveryAction(Request $request)
    {
        $form = $this->createForm(new DeliveryAddForm($this->getUser()));

        if ('POST' === $request->getMethod()) {
            $delivery = new Delivery();
            $delivery->setTransportGiven(Delivery::NOURISH);
            $delivery->setShipped(false);
            $delivery->setDepo($this->getDoctrine()->getRepository('RbsCoreBundle:Depo')->find($request->request->get('delivery_add_form')['depo']));

            $this->getDoctrine()->getRepository('RbsSalesBundle:Delivery')->createDelivery($delivery);
            foreach ($request->request->get('delivery_add_form')['orders'] as $order){
                $orderObj = $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->find($order);
                $delivery->addOrder($orderObj);
                $this->getDoctrine()->getRepository('RbsSalesBundle:Delivery')->update($delivery);
                $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->update($orderObj);
            }
            
            $this->get('session')->getFlashBag()->add(
                'success',
                'Delivery Add Successfully'
            );

            return $this->redirect($this->generateUrl('deliveries_home'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/order_item_quantity", name="order_item_quantity", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function orderItemQuantityAction(Request $request)
    {
        $orderItemId = $request->request->get('orderItemId');
        $deliveryItems = $this->getDoctrine()->getRepository('RbsSalesBundle:DeliveryItem')->findOneBy(array(
            'orderItem' => $orderItemId
        ));
        $orderItemQuantity = 0;
        foreach ($deliveryItems as $deliveryItem){
            $orderItemQuantity += $deliveryItem->getQty();
        }
        
        $response = new Response(json_encode(array("orderItemQuantity" => $orderItemQuantity)), 200);

        return $response;
    }
}