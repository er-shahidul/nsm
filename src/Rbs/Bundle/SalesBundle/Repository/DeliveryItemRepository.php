<?php

namespace Rbs\Bundle\SalesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\SalesBundle\Entity\Delivery;
use Rbs\Bundle\SalesBundle\Entity\Order;

/**
 * DeliveryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DeliveryItemRepository extends EntityRepository
{
    public function getPartialDeliveredItems(Delivery $delivery)
    {
        $orders = $delivery->getOrders();
        $data = array();
        foreach ($orders as $order) {
            $query = $this->createQueryBuilder('deliveryItem');
            $query->join('deliveryItem.orderItem', 'orderItem');
            $query->join('orderItem.item', 'item');
            $query->select('SUM(deliveryItem.qty) AS delivered, orderItem.quantity, item.name AS itemName, orderItem.id');
            $query->groupBy('deliveryItem.orderItem');
//            $query->having('orderItem.quantity >= delivered');
            $query->where("deliveryItem.order = :order")->setParameter('order', $order);
            $result = $query->getQuery()->getResult();
            foreach ($result as $row) {
                $row['orderId'] = $order->getId();
                $data[$row['orderId']][$row['id']] = $row;
            }
        }
        return $data;
    }

    public function getTotalDeliveryItemByOrder(Order $order)
    {
        $query = $this->createQueryBuilder('d');
        $query
            ->select('SUM(d.qty) AS totalQuantity')
            ->where('d.order = :order')
            ->setParameter('order', $order);

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getDeliveredItems(Order $order)
    {
        $query = $this->createQueryBuilder('di');
        $query->join('di.delivery', 'd');
        $query->join('di.orderItem', 'oi');
        $query->join('oi.item', 'i');
        $query->select('i.id');
        $query->addSelect('i.name');
        $query->addSelect('SUM(di.qty) AS totalDeliveredQuantity');
        $query->where('di.order = :order');
        $query->setParameter('order', $order->getId());
        $query->groupBy('di.orderItem');
        $query->orderBy('di.orderItem', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function getDeliveryIncentive($deliveryId)
    {
        $query = $this->createQueryBuilder('di');
        $query->join('di.delivery', 'd');
        $query->join('di.order', 'o');
        $query->join('o.agent', 'a');
        $query->join('di.orderItem', 'oi');
        $query->join('oi.item', 'i');
        $query->join('i.itemType', 'it');
        $query->join('d.depo', 'depo');
        $query->select('di.qty as quantity');
        $query->addSelect('a.id as agentId');
        $query->addSelect('it.id as itemTypeId');
        $query->addSelect('depo.id as depoId');
        $query->addSelect('it.itemType as itemType');
        $query->where('d.id = :deliveryId');
        $query->setParameters(array('deliveryId'=>$deliveryId));

        return $query->getQuery()->getResult();
    }
}