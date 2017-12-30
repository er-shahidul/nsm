<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Vehicle
 *
 * @ORM\Table(name="sales_vehicles")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\VehicleRepository")
 * @ORMSubscribedEvents()
 * @ORM\HasLifecycleCallbacks
 */
class Vehicle
{
    const ACTIVE = 'ACTIVE';
    const INACTIVE = 'INACTIVE';
    const UNREAD = 'UNREAD';
    const READ = 'READ';
    
    const NOURISH = 'NOURISH';
    const AGENT = 'AGENT';
    
    const FINISH_LOAD = 'FINISH LOAD';
    const START_LOAD = 'START LOAD';
    const IN = 'IN';
    const OUT = 'OUT';
    const CREATE = 'CREATE';

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
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Agent")
     * @ORM\JoinColumn(name="agent_id", nullable=true)
     */
    private $agent;

    /**
     * @var Depo
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Depo")
     * @ORM\JoinColumn(name="depo_id", nullable=true)
     */
    private $depo;

    /**
     * @var Delivery
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Delivery", inversedBy="vehicles")
     * @ORM\JoinColumn(name="deliveries_id", nullable=true)
     */
    private $deliveries;

    /**
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('ACTIVE', 'INACTIVE', 'READ', 'UNREAD')", nullable=false)
     */
    private $status = 'ACTIVE';

    /**
     * @var boolean
     *
     * @ORM\Column(name="shipped", type="boolean", nullable=true)
     */
    private $shipped = false;

    /**
     * @var string
     *
     * @ORM\Column(name="order_text", type="text", nullable=true)
     */
    private $orderText;

    /**
     * @var array $type
     *
     * @ORM\Column(name="transport_status", type="string", length=255, columnDefinition="ENUM('CREATE', 'IN', 'OUT', 'START LOAD', 'FINISH LOAD')", nullable=false)
     */
    private $transportStatus = 'CREATE';

    /**
     * @var array $type
     *
     * @ORM\Column(name="transport_given", type="string", length=255, columnDefinition="ENUM('NOURISH', 'AGENT')", nullable=true)
     */
    private $transportGiven;
    
    /**
     * @var string
     *
     * @ORM\Column(name="driver_name", type="text", nullable=true)
     */
    private $driverName;

    /**
     * @var string
     *
     * @ORM\Column(name="sms_text", type="text", nullable=true)
     */
    private $smsText;

    /**
     * @var string
     *
     * @ORM\Column(name="driver_phone", type="text", nullable=true)
     */
    private $driverPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="truck_number", type="text", nullable=true)
     */
    private $truckNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", nullable=true)
     */
    private $remark;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vehicle_in", type="datetime", nullable=true)
     */
    private $vehicleIn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vehicle_out", type="datetime", nullable=true)
     */
    private $vehicleOut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_load", type="datetime", nullable=true)
     */
    private $startLoad;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finish_load", type="datetime", nullable=true)
     */
    private $finishLoad;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="vehicle_invoice_attached_By", nullable=true)
     */
    private $truckInvoiceAttachedBy;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vehicle_invoice_attached_at", type="datetime", nullable=true)
     */
    private $truckInvoiceAttachedAt;
    
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
     * @return User
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param User $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }
    
    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param array $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getDriverName()
    {
        return $this->driverName;
    }

    /**
     * @param string $driverName
     */
    public function setDriverName($driverName)
    {
        $this->driverName = $driverName;
    }

    /**
     * @return string
     */
    public function getDriverPhone()
    {
        return $this->driverPhone;
    }

    /**
     * @param string $driverPhone
     */
    public function setDriverPhone($driverPhone)
    {
        $this->driverPhone = $driverPhone;
    }

    /**
     * @return string
     */
    public function getTruckNumber()
    {
        return $this->truckNumber;
    }

    /**
     * @param string $truckNumber
     */
    public function setTruckNumber($truckNumber)
    {
        $this->truckNumber = $truckNumber;
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

    /**
     * @return \DateTime
     */
    public function getVehicleIn()
    {
        return $this->vehicleIn;
    }

    /**
     * @param \DateTime $vehicleIn
     */
    public function setVehicleIn($vehicleIn)
    {
        $this->vehicleIn = $vehicleIn;
    }

    /**
     * @return \DateTime
     */
    public function getVehicleOut()
    {
        return $this->vehicleOut;
    }

    /**
     * @param \DateTime $vehicleOut
     */
    public function setVehicleOut($vehicleOut)
    {
        $this->vehicleOut = $vehicleOut;
    }

    /**
     * @return \DateTime
     */
    public function getStartLoad()
    {
        return $this->startLoad;
    }

    /**
     * @param \DateTime $startLoad
     */
    public function setStartLoad($startLoad)
    {
        $this->startLoad = $startLoad;
    }

    /**
     * @return \DateTime
     */
    public function getFinishLoad()
    {
        return $this->finishLoad;
    }

    /**
     * @param \DateTime $finishLoad
     */
    public function setFinishLoad($finishLoad)
    {
        $this->finishLoad = $finishLoad;
    }

    public function isIn()
    {
        if($this->vehicleIn == null){
            return true;
        }
        return false;
    }

    public function isOut()
    {
        if($this->vehicleOut == null){
            return true;
        }
        return false;
    }

    public function isStart()
    {
        if($this->startLoad == null){
            return true;
        }
        return false;
    }

    public function isFinish()
    {
        if($this->finishLoad == null){
            return true;
        }
        return false;
    }

    public function isDeliveryTrue()
    {
        if($this->deliveries == null){
            return true;
        }
        return false;
    }

    public function isDeliveryFalse()
    {
        if($this->deliveries == null){
            return false;
        }
        return true;
    }

    public function isDeliveryShipped()
    {
        if($this->deliveries->isShipped() == false){
            return false;
        }
        return true;
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
     * @return User
     */
    public function getTruckInvoiceAttachedBy()
    {
        return $this->truckInvoiceAttachedBy;
    }

    /**
     * @param User $truckInvoiceAttachedBy
     */
    public function setTruckInvoiceAttachedBy($truckInvoiceAttachedBy)
    {
        $this->truckInvoiceAttachedBy = $truckInvoiceAttachedBy;
    }

    /**
     * @return \DateTime
     */
    public function getTruckInvoiceAttachedAt()
    {
        return $this->truckInvoiceAttachedAt;
    }

    /**
     * @return string
     */
    public function getSmsText()
    {
        return $this->smsText;
    }

    /**
     * @param string $smsText
     */
    public function setSmsText($smsText)
    {
        $this->smsText = $smsText;
    }

    /**
     * @param \DateTime $truckInvoiceAttachedAt
     */
    public function setTruckInvoiceAttachedAt($truckInvoiceAttachedAt)
    {
        $this->truckInvoiceAttachedAt = $truckInvoiceAttachedAt;
    }
    
    public function getTruckInformation()
    {
        return '#' . ' Truck SL :'. $this->getId() . ',' . ' Driver Name :' . $this->getDriverName() . ',' . ' Mobile :' . $this->getDriverPhone();
    }

    /**
     * @return array
     */
    public function getTransportStatus()
    {
        return $this->transportStatus;
    }

    /**
     * @param array $transportStatus
     */
    public function setTransportStatus($transportStatus)
    {
        $this->transportStatus = $transportStatus;
    }

    /**
     * @return mixed
     */
    public function getOrderText()
    {
        return $this->orderText;
    }

    /**
     * @param mixed $orderText
     */
    public function setOrderText($orderText)
    {
        $this->orderText = $orderText;
    }

    /**
     * @return mixed
     */
    public function getShipped()
    {
        return $this->shipped;
    }

    /**
     * @param mixed $shipped
     */
    public function setShipped($shipped)
    {
        $this->shipped = $shipped;
    }
    
    public function getName()
    {
        return !empty($this->getAgent()->getName())
            ? $this->getAgent()->getName()
            : $this->getAgent()->getUsername();
    }
}