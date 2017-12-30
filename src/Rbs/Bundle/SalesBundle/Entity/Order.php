<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Rbs\Bundle\CoreBundle\Entity\ItemType;
use Rbs\Bundle\CoreBundle\Entity\Location;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Order
 *
 * @ORM\Table(name="sales_orders")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\OrderRepository")
 * @ORMSubscribedEvents()
 */
class Order
{
    const ORDER_STATE_PENDING = 'PENDING';
    const ORDER_STATE_HOLD = 'HOLD';
    const ORDER_STATE_COMPLETE = 'COMPLETE';
    const ORDER_STATE_CANCEL = 'CANCEL';
    const ORDER_STATE_PROCESSING = 'PROCESSING';

    const PAYMENT_STATE_PENDING = 'PENDING';
    const PAYMENT_STATE_PARTIALLY_PAID = 'PARTIALLY_PAID';
    const PAYMENT_STATE_PAID = 'PAID';
    const PAYMENT_STATE_APPROVAL = 'APPROVAL';
    const PAYMENT_STATE_CREDIT_APPROVAL = 'CREDIT_APPROVAL';

    const DELIVERY_STATE_PENDING = 'PENDING';
    const DELIVERY_STATE_HOLD = 'HOLD';
    const DELIVERY_STATE_READY = 'READY';
    const DELIVERY_STATE_PARTIALLY_SHIPPED = 'PARTIALLY_SHIPPED';
    const DELIVERY_STATE_SHIPPED = 'SHIPPED';

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
     * @ORM\OneToOne(targetEntity="OrderIncentiveFlag", mappedBy="order", cascade={"persist"})
     */
    protected $orderIncentiveFlag;

    /**
     * @ORM\ManyToMany(targetEntity="Payment", mappedBy="orders")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     **/
    protected $payments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Rbs\Bundle\SalesBundle\Entity\OrderItem", mappedBy="order", cascade={"persist", "remove"}, orphanRemoval=true))
     */
    private $orderItems;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Agent")
     * @ORM\JoinColumn(name="agent_id")
     */
    private $agent;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Location")
     * @ORM\JoinColumn(name="location_id", nullable=true)
     */
    private $location;

    /**
     * @var array $type
     *
     * @ORM\Column(name="delivery_state", type="string", length=255, columnDefinition="ENUM('PENDING', 'HOLD', 'READY', 'PARTIALLY_SHIPPED', 'SHIPPED')", nullable=true)
     */
    private $deliveryState;

    /**
     * @var array $type
     *
     * @ORM\Column(name="payment_state", type="string", length=255, columnDefinition="ENUM('PENDING', 'PARTIALLY_PAID', 'PAID', 'CREDIT_APPROVAL')", nullable=true)
     */
    private $paymentState;

    /**
     * @var array $type
     *
     * @ORM\Column(name="order_state", type="string", length=255, columnDefinition="ENUM('PENDING', 'HOLD', 'PROCESSING', 'COMPLETE', 'CANCEL')", nullable=true)
     */
    private $orderState;

    /**
     * @var float
     *
     * @ORM\Column(name="total_amount", type="float")
     */
    private $totalAmount = 0 ;

    /**
     * @var float
     *
     * @ORM\Column(name="paid_amount", type="float")
     */
    private $paidAmount = 0 ;

    /**
     * @var string
     *
     * @ORM\Column(name="order_via", type="string", length=250, nullable=true)
     */
    private $orderVia = 'SYSTEM';

    /**
     * @ORM\OneToOne(targetEntity="Sms", mappedBy="order", cascade={"persist"})
     * @Assert\NotBlank()
     */
    protected $refSMS;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", nullable=true)
     */
    private $remark;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Delivery", mappedBy="orders")
     */
    private $deliveries;

    /**
     * @var Depo
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Depo")
     * @ORM\JoinColumn(name="depo_id", nullable=true)
     */
    private $depo;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    /**
     * @param \Rbs\Bundle\SalesBundle\Entity\OrderItem $order
     */
    public function addOrder($order)
    {
        if (!$this->getOrderItems()->contains($order)) {
            $order->setOrder($this);
            $this->getOrderItems()->add($order);
        }
    }

    /**
     * @param \Rbs\Bundle\SalesBundle\Entity\OrderItem $order
     */
    public function removeOrder($order)
    {
        if ($this->getOrderItems()->contains($order)) {
            $this->getOrderItems()->removeElement($order);
        }
    }

    public function addOrderItem(OrderItem $item)
    {
        if (!$this->orderItems->contains($item)) {
            $this->orderItems->add($item);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $item)
    {
        $item->setOrder(null);
        $this->orderItems->removeElement($item);
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @return string
     */
    public function getDeliveryState()
    {
        return $this->deliveryState;
    }

    /**
     * @param string $deliveryState
     */
    public function setDeliveryState($deliveryState)
    {
        $this->deliveryState = $deliveryState;
    }

    /**
     * @return string
     */
    public function getPaymentState()
    {
        return $this->paymentState;
    }

    /**
     * @param string $paymentState
     */
    public function setPaymentState($paymentState)
    {
        $this->paymentState = $paymentState;
    }

    /**
     * @return string
     */
    public function getOrderState()
    {
        return $this->orderState;
    }

    /**
     * @param string $orderState
     */
    public function setOrderState($orderState)
    {
        $this->orderState = $orderState;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return float
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * @param float $paidAmount
     */
    public function setPaidAmount($paidAmount)
    {
        $this->paidAmount = $paidAmount;
    }

    /**
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return string
     */
    public function getOrderVia()
    {
        return $this->orderVia;
    }

    /**
     * @param string $orderVia
     */
    public function setOrderVia($orderVia)
    {
        $this->orderVia = $orderVia;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return Sms
     */
    public function getRefSMS()
    {
        return $this->refSMS;
    }

    /**
     * @param mixed $refSMS
     */
    public function setRefSMS($refSMS)
    {
        $this->refSMS = $refSMS;
    }

    /** @return float */
    public function getItemsTotalAmount()
    {
        $total = 0;
        /** @var OrderItem $item */
        foreach($this->orderItems as $item) {
            $total += $item->getTotalAmount();
        }

        return $total;
    }

    public function isPending()
    {
        $state = false;
        if($this->orderState == 'PENDING'){
            $state = true;
        }

        return $state;
    }

    public function checkDeliveryState()
    {
        if($this->getDeliveryState() == Order::DELIVERY_STATE_READY 
            or $this->getDeliveryState() == Order::DELIVERY_STATE_PARTIALLY_SHIPPED
            or $this->getDeliveryState() == Order::ORDER_STATE_CANCEL){
            return true;
        }

        return false;
    }

    public function isComplete()
    {
        $state = false;
        if($this->orderState == 'COMPLETE'){
            $state = true;
        }

        return $state;
    }

    public function isCancel()
    {
        $state = false;
        if($this->orderState == 'CANCEL'){
            $state = true;
        }

        return $state;
    }
    
    public function getOrderItemType()
    {
        foreach ($this->orderItems as $item){
            if($item->getItem()->getItemType()->getItemType() == ItemType::Chick){
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param mixed $payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    public function getOrderIdAndDueAmount()
    {
        return '#' . ' '. $this->getId() . ' Due amount :' . ($this->getTotalAmount() - $this->getPaidAmount());
    }

    public function getOrderIdAndAgent()
    {
        return '#' . ' '. $this->getId() . ' Agent :' . $this->getAgent()->getUser()->getUsername();
    }

    public function getOrderInfo()
    {
        $date = $this->getCreatedAt()->setTimezone(new \DateTimeZone( 'Asia/Dhaka' ));
        return '#' . ' Order Id :'. $this->getId() . ', Amount :' . $this->getPaidAmount() .', Date:'. $date->format('Y-F-d, h:i A');
    }

    public function getOrderInfoForDamageGoods()
    {
        $date = $this->getCreatedAt()->setTimezone(new \DateTimeZone( 'Asia/Dhaka' ));
        return '#' . ' Order Id :'. $this->getId(). ' Depo :'. $this->getDepo()->getName(). ', Agent :' .
        ($this->getAgent()->getUser()->getProfile()->getFullName() ? $this->getAgent()->getUser()->getProfile()->getFullName()
            : $this->getAgent()->getUser()->getUsername()). ', Date : '. $date->format('Y-F-d, h:i A');
    }

    public function getOrderInfoWithAgent()
    {
        $date = $this->getCreatedAt()->setTimezone(new \DateTimeZone( 'Asia/Dhaka' ));
        return '#' . ' Order Id :'. $this->getId(). ', Agent :' .
            ($this->getAgent()->getUser()->getProfile()->getFullName() ? $this->getAgent()->getUser()->getProfile()->getFullName()
            : $this->getAgent()->getUser()->getUsername()). ', Date : '. $date->format('Y-F-d, h:i A');
    }

    public function getDueAmount()
    {
        return ($this->getTotalAmount() - $this->getPaidAmount());
    }

    /**
     * @return mixed
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * @param mixed $deliveries
     */
    public function setDeliveries($deliveries)
    {
        $this->deliveries = $deliveries;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function categorySum()
    {
        $data = array();
        /** @var OrderItem $orderItem */
        foreach ($this->orderItems as $orderItem) {
            $categoryId = $orderItem->getItem()->getFirstCategory()->getId();
            
            if (!isset($data[$categoryId])) {
                $data[$categoryId] = 0;
            }

            $data[$categoryId] += $orderItem->getTotalAmount();
        }
        
        return $data;
    }

    /**
     * @return OrderIncentiveFlag
     */
    public function getOrderIncentiveFlag()
    {
        return $this->orderIncentiveFlag;
    }

    /**
     * @param mixed $orderIncentiveFlag
     */
    public function setOrderIncentiveFlag($orderIncentiveFlag)
    {
        $this->orderIncentiveFlag = $orderIncentiveFlag;
    }

    /**
     * @return Depo
     */
    public function getDepo()
    {
        return $this->depo;
    }

    /**
     * @param Depo $depo
     */
    public function setDepo($depo)
    {
        $this->depo = $depo;
    }

    public function calculateOrderAmount()
    {
        /** @var OrderItem $orderItems */
        foreach ($this->getOrderItems() as $orderItems) {
            $orderItems->setOrder($this);
            $orderItems->calculateTotalAmount(true);
        }
        $this->setTotalAmount($this->getItemsTotalAmount());
    }

    public function getTotalPaymentDepositedAmount()
    {
        $data = 0;
        /** @var Payment $payment */
        foreach ($this->getPayments() as $payment) {
            $data+= $payment->getDepositedAmount();
        }
        return $data;
    }

    public function getTotalPaymentActualAmount()
    {
        $data = 0;
        /** @var Payment $payment */
        foreach ($this->getPayments() as $payment) {
            $data+= $payment->getAmount();
        }
        return $data;
    }
}