<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Entity\ItemType;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\OrderIncentiveFlag;
use Rbs\Bundle\SalesBundle\Entity\OrderItem;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Form\Type\OrderForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Order Controller.
 *
 */
class ChickOrderController extends BaseController
{
    /**
     * @Route("/orders/manage/chick", name="order_manage_chick")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_CHICK_ORDER_MANAGE")
     */
    public function indexAction(Request $request)
    {
        $regions = array();
        $orderItemResult = array();
        $item = null;
        $date = $request->query->get('date') ? date('Y-m-d', strtotime($request->query->get('date'))) : null;
        $chickItems = $this->getChickItems();
        if ($itemId = $request->query->get('item')) {
            $item = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->find($itemId);
        }

        if ($item && $date) {
            $orderItemResult = $this->getChickOrders($date, $item, [Order::ORDER_STATE_PENDING]);
            $regions = $this->getRegions();
        }

        return $this->render('RbsSalesBundle:ChickOrder:manage-order.html.twig', array(
                'orderItemResult' => $orderItemResult,
                'chickItems'      => $chickItems,
                'date'            => $request->query->get('date'),
                'regions'         => $regions,
                'selectedItem'    => $itemId,
            )
        );
    }

    /**
     * @Route("/orders/manage/chick/save", name="order_manage_chick_save", options={"expose"=true})
     * @JMS\Secure(roles="ROLE_CHICK_ORDER_MANAGE")
     */
    public function saveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('orderItem');
        $deliver = $request->request->get('deliver');

        $output = ['skipped' => [], 'update' => []];

        try {
            foreach ($data as $orderId => $orderItem) {
                foreach ($orderItem as $orderItemId => $orderItemValue) {
                    /** @var OrderItem $orderItem */
                    if (!$orderItem = $em->getRepository('RbsSalesBundle:OrderItem')->find($orderItemId)) {
                        $output['skipped'][] = $orderItemId;
                        continue;
                    }

                    if (!$order = $em->getRepository('RbsSalesBundle:Order')->find($orderId)) {
                        continue;
                    }

                    $output['old'][$orderId] = $order->getItemsTotalAmount();

                    $output['update'][] = $orderItemId;
                    $orderItem->setQuantity((int) $orderItemValue);

                    $order->calculateOrderAmount();

                    $output['new'][$orderId] = $order->getTotalAmount();

                    if ($deliver == 'true') {
                        $order->setDeliveryState(Order::DELIVERY_STATE_READY);
                        $order->setOrderState(Order::ORDER_STATE_PROCESSING);
                        $order->setPaymentState(Order::PAYMENT_STATE_PENDING);
                    }

                    $em->persist($orderItem);
                    $em->persist($order);
                }

                $em->flush();
            }

        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return new JsonResponse(array_merge($output, ['success' => true]));
    }

    /**
     * @Route("/orders/view-summary/chick", name="order_manage_chick_summary", options={"expose"=true})
     * @JMS\Secure(roles="ROLE_CHICK_ORDER_MANAGE")
     */
    public function viewSummaryAction(Request $request)
    {
        $regions = array();
        $orderItemResult = array();
        $item = null;
        $date = $request->query->get('date') ? date('Y-m-d', strtotime($request->query->get('date'))) : null;
        $chickItems = $this->getChickItems();
        if ($itemId = $request->query->get('item')) {
            $item = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->find($itemId);
        }

        if ($item && $date) {
            $orderItemResult = $this->getChickOrders($date, $item, [Order::ORDER_STATE_PROCESSING, Order::ORDER_STATE_COMPLETE]);
            $regions = $this->getRegions();
        }

        return $this->render('RbsSalesBundle:ChickOrder:view-summary.html.twig', array(
                'orderItemResult' => $orderItemResult,
                'chickItems'      => $chickItems,
                'date'            => $request->query->get('date'),
                'regions'         => $regions,
                'selectedItem'    => $itemId,
            )
        );
    }

    private function getRegions() {
        $regionResult = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->findBy(array('level' => 3));
        $regions = array();
        foreach ($regionResult as $row) {
            $regions[$row->getId()] = $row->getName();
        }

        return $regions;
    }

    private function getChickItems() {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('RbsCoreBundle:Item')->findBy(
            array(
                'itemType' => $em->getRepository('RbsCoreBundle:ItemType')->findOneBy(
                    array(
                        'itemType' => ItemType::Chick
                    )
                )
            )
        );
    }

    private function getChickOrders($date, $item, $orderState) {
        $repo = $this->getDoctrine()->getRepository('RbsSalesBundle:OrderItem');
        $qb = $repo->createQueryBuilder('oi');

        $qb->select('oi');
        $qb->join('oi.order', 'o');
        $qb->join('o.agent', 'a');
        $qb->join('a.user', 'u');
        $qb->join('u.profile', 'p');
        $qb->join('u.zilla', 'z');

        $qb->where($qb->expr()->between('o.createdAt', ':start', ':end'));
        $qb->setParameters(array('start' => $date . ' 00:00:00', 'end' => $date . ' 23:59:59'));

        $qb->andWhere('oi.item = :item')->setParameter('item', $item);

        //$qb->andWhere('o.orderState IN :orderState')->setParameter('orderState', $orderState);
        $qb->andWhere($qb->expr()->in('o.orderState', ':orderState'))->setParameter('orderState', $orderState);

        $qb->orderBy('z.parentId');
        $qb->addOrderBy('z.name');
        $qb->addOrderBy('p.fullName');
        $result = $qb->getQuery()->getResult();
        $orderItemResult = array();
        foreach ($result as $row) {
            $orderItemResult[$row->getOrder()->getAgent()->getUser()->getZilla()->getParentId()][] = $row;
        }

        return $orderItemResult;
    }
}