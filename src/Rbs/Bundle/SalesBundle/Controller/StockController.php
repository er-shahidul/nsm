<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Rbs\Bundle\CoreBundle\Entity\Item;
use Rbs\Bundle\CoreBundle\Entity\ItemType;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\Stock;
use Rbs\Bundle\SalesBundle\Entity\StockHistory;
use Rbs\Bundle\SalesBundle\Form\Type\StockHistoryForm;
use Rbs\Bundle\SalesBundle\RbsSalesBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Stock Controller.
 *
 */
class StockController extends Controller
{
    /**
     * @Route("/stocks", name="stocks_home")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_STOCK_VIEW, ROLE_STOCK_CREATE")
     */
    public function indexAction(Request $request)
    {
        $datatable = $this->get('rbs_erp.sales.datatable.stock');
        $datatable->buildDatatable();

        if ('POST' === $request->getMethod()) {
            $stockHistory = new StockHistory();
            $form = $this->createForm(new StockHistoryForm(), $stockHistory);
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')
                    ->save($request, $form, $stockHistory);

                $this->get('session')->getFlashBag()->add(
                    'success', 'Stock Item Quantity Added Successfully'
                );

                return $this->redirect($this->generateUrl('stocks_home'));
            }
        }

        return $this->render('RbsSalesBundle:Stock:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * Lists all Category entities.
     *
     * @Route("/stocks_list_ajax", name="stocks_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_STOCK_VIEW, ROLE_STOCK_CREATE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.stock');
        $datatable->buildDatatable();

        $user = $this->getUser()->getId();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function ($qb)  use ($user)
        {
            $qb->join("sales_stocks.item", 'i');
            $qb->join("i.itemType", 'it');
            $qb->join("sales_stocks.depo", 'd');
            $qb->join("i.bundles", 'bundles');
            $qb->join('d.users', 'u');
            $qb->andWhere("sales_stocks.deletedAt IS NULL");
            $qb->andWhere("it.itemType != :chicken")->setParameter('chicken', ItemType::Chick);
            $qb->andWhere("d.deletedAt IS NULL");
            // Show only Sales Bundle
            $qb->andWhere("bundles.id = :bundle")->setParameter('bundle', RbsSalesBundle::ID);
            $qb->andWhere("u.id = :user");
            $qb->setParameter('user', $user);

        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * find stock item ajax
     * @Route("find_stock_item_ajax", name="find_stock_item_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @JMS\Secure(roles="ROLE_ORDER_VIEW, ROLE_STOCK_VIEW, ROLE_STOCK_CREATE")
     */
    public function findItemAction(Request $request)
    {
        $item = $request->request->get('item');
        $agent = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->find($request->request->get('agent'));
        $order = $this->getDoctrine()->getRepository('RbsSalesBundle:Order')->find($request->request->get('orderId'));

        $stock = $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->findOneBy(array(
            'item' => $item,
            'depo' => $order->getDepo()->getId()
        ));

        $response = array(
            'onHand'    => $stock->getOnHand(),
            'onHold'    => $stock->getOnHold(),
            'available' => $stock->isAvailableOnDemand(),
            'price'     => $stock->getItem()->getPrice(),
            'itemUnit'  => $stock->getItem()->getItemUnit(),
        );

        return new JsonResponse($response);
    }

    /**
     * find stock item ajax
     * @Route("find_stock_item_depo_ajax/{item}/{agent}/{depo}", name="find_stock_item_depo_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @JMS\Secure(roles="ROLE_ORDER_VIEW, ROLE_STOCK_VIEW, ROLE_STOCK_CREATE")
     */
    public function findItemDepoAction(Request $request, Item $item, Agent $agent, Depo $depo)
    {
        $em = $this->getDoctrine()->getManager();
        if ($depo == null) {
            $depo = $agent->getDepo();
        }

        /** Getting Item Price */
        $price = 0;
        $orderItem = null;
        $order = $em->getRepository('RbsSalesBundle:Order')->find($request->query->get('order', 0));
        if ($order) { // edit mode and item already added
            $orderItem = $em->getRepository('RbsSalesBundle:OrderItem')->findOneBy(
                array('order' => $order, 'item' => $item)
            );
            $price = $orderItem ? $orderItem->getPrice() : 0;
        }

        // new entry or new item add to edit mode
        if (!$price) {
            $price = $this->getDoctrine()->getRepository('RbsCoreBundle:ItemPrice')->getCurrentPrice(
                $item, $depo->getLocation()
            );
        }

        if ($item->getItemType() == ItemType::Chick) {
            $chickenSetForAgent = $this->getDoctrine()->getRepository('RbsSalesBundle:ChickenSetForAgent')->findOneBy(
                array(
                    'item'  => $item,
                    'agent' => $agent,
                )
            );

            // If edit mode, add current qty
            $response = array(
                'onHand'    => $chickenSetForAgent ? $chickenSetForAgent->getQuantity() : 0,
                'onHold'    => 0,
                'available' => 0,
                'price'     => number_format($price, 2),
                'itemUnit'  => $chickenSetForAgent ? $chickenSetForAgent->getItem()->getItemUnit() : 'Pc',
            );
        } else {
            $stock = $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->findOneBy(
                array(
                    'item' => $item,
                    'depo' => $depo,
                )
            );

            // If edit mode, add current qty
            $qtyInThisOrder = $orderItem && $order->getOrderState() == Order::ORDER_STATE_PROCESSING ? $orderItem->getQuantity() : 0;
            $response = array(
                'onHand'    => $stock->getOnHand() + $qtyInThisOrder,
                'onHold'    => $stock->getOnHold(),
                'available' => $stock->isAvailableOnDemand(),
                'price'     => number_format($price, 2),
                'itemUnit'  => $stock->getItem()->getItemUnit(),
            );
        }

        return new JsonResponse($response);
    }

    /**
     * @param $stock
     */
    protected function checkAvailableOnDemand(Stock $stock)
    {
        if ($stock->getOnHand() < $stock->getOnHold()) {
            $stock->setAvailableOnDemand(0);
            $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->update($stock);
        } elseif ($stock->getOnHand() >= $stock->getOnHold()) {
            $stock->setAvailableOnDemand(1);
            $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->update($stock);
        }
    }

    /**
     * @Route("/stock/{id}/create", name="stock_create", options={"expose"=true})
     * @Template("RbsSalesBundle:Stock:new.html.twig")
     * @param Stock $stock
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_STOCK_CREATE")
     */
    public function createAction(Stock $stock)
    {
        $stockHistory = new StockHistory();
        $form = $this->createForm(new StockHistoryForm(), $stockHistory);

        return array(
            'form' => $form->createView(),
            'stock' => $stock
        );
    }

    /**
     * @Route("/stock/history", name="stock_history", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_STOCK_VIEW, ROLE_STOCK_CREATE")
     */
    public function stockHistoryAction(Request $request)
    {
        $stock = $request->query->all()['id'];
        $stockHistories = $this->getDoctrine()->getRepository('RbsSalesBundle:StockHistory')->findBy(
            array('stock' => $stock), array('id' => 'DESC'), 10
        );

        return $this->render('RbsSalesBundle:Stock:history.html.twig', array(
            'stockHistories' => $stockHistories,
            'stock' => $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->find($stock)
        ));
    }

    /**
     * @Route("/stock/available", name="stock_available", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_STOCK_VIEW, ROLE_STOCK_CREATE")
     */
    public function stockAvailableAction(Request $request)
    {
        $stockId = $request->query->all()['id'];
        $stock = $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->find($stockId);

        $available = $this->isAvailable($stock);

        $stock->setAvailableOnDemand($available);
        $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->update($stock);

        $this->get('session')->getFlashBag()->add(
            'success', 'Stock Item Available Set Successfully'
        );
        return $this->redirect($this->generateUrl('stocks_home'));
    }

    /**
     * @param Stock $stock
     * @return int
     */
    protected function isAvailable(Stock $stock)
    {
        if ($stock->isAvailableOnDemand()) {
            $available = 0;
            return $available;
        } else {
            $available = 1;
            return $available;
        }
    }
}