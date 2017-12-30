<?php

namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Rbs\Bundle\UserBundle\Entity\User;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Delivery
 *
 * @ORM\Table(name="sales_deliveries")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\DeliveryRepository")
 * @ORMSubscribedEvents()
 */
class Delivery
{
    const NOURISH = "NOURISH";
    const AGENT = "AGENT";
    
    use ORMBehaviors\Timestampable\Timestampable,
        ORMBehaviors\SoftDeletable\SoftDeletable,
        ORMBehaviors\Blameable\Blameable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="otherInfo", type="text", nullable=true)
     */
    private $otherInfo;

    /**
     * @ORM\ManyToMany(targetEntity="Order", inversedBy="deliveries")
     * @ORM\JoinTable(name="sales_join_deliveries_orders",
     *      joinColumns={@ORM\JoinColumn(name="delivery_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")}
     * )
     */
    protected $orders;

    /**
     * @var Depo
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Depo")
     * @ORM\JoinColumn(name="depo_id", nullable=false)
     */
    private $depo;

    /**
     * @var DeliveryItem
     *
     * @ORM\OneToMany(targetEntity="Rbs\Bundle\SalesBundle\Entity\DeliveryItem", mappedBy="delivery")
     **/
    private $deliveryItems;

    /**
     * @var Vehicle
     *
     * @ORM\OneToMany(targetEntity="Rbs\Bundle\SalesBundle\Entity\Vehicle", mappedBy="deliveries")
     */
    private $vehicles;
    
    /**
     * @var array $type
     *
     * @ORM\Column(name="transport_given", type="string", length=255, columnDefinition="ENUM('NOURISH', 'AGENT')", nullable=true)
     */
    private $transportGiven;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shipped", type="boolean", nullable=true)
     */
    private $shipped = false;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->deliveryItems = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set otherInfo
     *
     * @param string $otherInfo
     * @return Delivery
     */
    public function setOtherInfo($otherInfo)
    {
        $this->otherInfo = $otherInfo;

        return $this;
    }

    /**
     * Get otherInfo
     *
     * @return string 
     */
    public function getOtherInfo()
    {
        return $this->otherInfo;
    }

    /**
     * Set depo
     *
     * @param string $depo
     * @return Delivery
     */
    public function setDepo($depo)
    {
        $this->depo = $depo;

        return $this;
    }

    /**
     * Get depo
     *
     * @return Depo
     */
    public function getDepo()
    {
        return $this->depo;
    }

    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function getOrdersString()
    {
        $return = array();
        foreach ($this->orders as $order) {
            $return[] = $order->getId();
        }

        return implode(',', $return);
    }

    /**
     * @param \Rbs\Bundle\SalesBundle\Entity\Order $order
     */
    public function addOrder($order)
    {
        if (!$this->getOrders()->contains($order)) {
            $this->orders->add($order);
        }
    }

    /**
     * @param \Rbs\Bundle\SalesBundle\Entity\Order $order
     */
    public function removeOrder($order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * @return ArrayCollection
     */
    public function getDeliveryItems()
    {
        return $this->deliveryItems;
    }

    /**
     * @param DeliveryItem $deliveryItems
     *
     * @return Delivery
     */
    public function setDeliveryItems($deliveryItems)
    {
        $this->deliveryItems = $deliveryItems;

        return $this;
    }

    /**
     * @return array
     */
    public function getTransportGiven()
    {
        return $this->transportGiven;
    }

    /**
     * @param array $transportGiven
     */
    public function setTransportGiven($transportGiven)
    {
        $this->transportGiven = $transportGiven;
    }

    /**
     * @return Vehicle
     */
    public function getVehicles()
    {
        return $this->vehicles;
    }

    /**
     * @param Vehicle $vehicles
     */
    public function setVehicles($vehicles)
    {
        $this->vehicles = $vehicles;
    }

    public function isDeliveryAdd()
    {
        $orders = $this->getOrders();
        foreach ($orders as $order){
            if($order->getDeliveryState() != Order::DELIVERY_STATE_READY or $order->getDeliveryState() != Order::DELIVERY_STATE_PARTIALLY_SHIPPED){
               return false; 
            }
        }
        return true;
    }
    
    public function getOrderNumbers(){
        $orders = ' ';
        foreach ($this->getOrders() as $key => $order){
            $orders .= $order->getId() . ', ';
        }
        return $orders;
    }
    
    public function getDeliveryInfo()
    {
        return '#' . ' Delivery ID #'. $this->getId() . ',' . ' Order Number #' . $this->getOrderNumbers();
    }

    /**
     * @return boolean
     */
    public function isShipped()
    {
        return $this->shipped;
    }

    /**
     * @param boolean $shipped
     */
    public function setShipped($shipped)
    {
        $this->shipped = $shipped;
    }
}
