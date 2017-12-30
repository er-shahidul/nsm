<?php

namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\Item;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * DeliveryItem
 *
 * @ORM\Table(name="sales_delivery_items")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\DeliveryItemRepository")
 */
class DeliveryItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Delivery
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Delivery", inversedBy="deliveryItems")
     * @ORM\JoinColumn(name="delivery_id", nullable=false)
     */
    private $delivery;

    /**
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\OrderItem")
     * @ORM\JoinColumn(name="order_item_id", nullable=false)
     */
    private $orderItem;

    /**
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Order")
     * @ORM\JoinColumn(name="order_id")
     */
    private $order;

    /**
     * @var integer
     *
     * @ORM\Column(name="qty", type="integer")
     */
    private $qty;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Delivery
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param Delivery $delivery
     *
     * @return DeliveryItem
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param mixed $item
     *
     * @return DeliveryItem
     */
    public function setOrderItem($item)
    {
        $this->orderItem = $item;

        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     *
     * @return DeliveryItem
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     *
     * @return DeliveryItem
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

}