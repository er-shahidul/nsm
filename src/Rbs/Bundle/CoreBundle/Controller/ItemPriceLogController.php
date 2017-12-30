<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Rbs\Bundle\CoreBundle\Entity\Item;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * ItemPriceLog Controller.
 *
 */
class ItemPriceLogController extends BaseController
{
    /**
     * @Route("/item/{id}/price/log", name="ItemPriceLogs_home", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function indexAction(Request $request, Item $item)
    {
        $itemId = $item->getId();
        $itemHistories = $this->getDoctrine()->getRepository('RbsCoreBundle:ItemPriceLog')->findBy(array(
            'item' => $itemId
        ));

        $locationsArray = array();
        $locations = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')
            ->findBy(array('level' => 4), array('name' => 'ASC'));
        foreach ($locations as $location) {
            $locationsArray[] = $location->getName();
        }

        $datatable = $this->get('rbs_erp.core.datatable.ItemPriceLog');
        $datatable->buildDatatable();

        return $this->render('RbsCoreBundle:ItemPriceLog:index.html.twig', array(
            'itemHistories' => $itemHistories,
            'datatable' => $datatable,
            'item' => $item,
            'locations' => json_encode($locationsArray)
        ));
    }

    /**
     * Lists all ItemPriceLog entities.
     *
     * @Route("/items_price_log_list_ajax/{item}", name="items_price_log_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function listAjaxAction(Request $request)
    {
        $itemId = $request->attributes->get('item');
        $datatable = $this->get('rbs_erp.core.datatable.ItemPriceLog');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb) use ($itemId)
        {
            $qb->addOrderBy('core_item_price.active', 'DESC');
            $qb->andWhere("core_item_price.item = :item");
            $qb->setParameter("item", $itemId);
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }
}
