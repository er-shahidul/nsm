<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * CashReceive
 *
 * @ORM\Table(name="sales_cash_receives")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\CashReceiveRepository")
 * @ORMSubscribedEvents()
 */
class CashReceive
{
    const ACTIVE = 'ACTIVE';
    const INACTIVE = 'INACTIVE';

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
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('ACTIVE', 'INACTIVE')", nullable=false)
     */
    private $status = 'ACTIVE';

    /**
     * @var float
     *
     * @ORM\Column(name="amounts", type="float", nullable=false)
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="total_received_amount", type="float", nullable=false)
     * @Assert\NotBlank()
     */
    private $totalReceivedAmount;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="received_by")
     */
    private $receivedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received_at", type="datetime", nullable=true)
     */
    private $receivedAt;
    
    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Order")
     * @ORM\JoinColumn(name="order_id")
     */
    private $orderRef;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Agent")
     * @ORM\JoinColumn(name="agent_id")
     * @Assert\NotBlank()
     */
    private $agent;

    /**
     * @var Depo
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Depo")
     * @ORM\JoinColumn(name="depo_id")
     */
    private $depo;

    /**
     * @var string
     *
     * @ORM\Column(name="depositors", type="text", nullable=true)
     */
    private $depositor;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", nullable=true)
     */
    private $remark;

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
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getTotalReceivedAmount()
    {
        return $this->totalReceivedAmount;
    }

    /**
     * @param float $totalReceivedAmount
     */
    public function setTotalReceivedAmount($totalReceivedAmount)
    {
        $this->totalReceivedAmount = $totalReceivedAmount;
    }

    /**
     * @return User
     */
    public function getReceivedBy()
    {
        return $this->receivedBy;
    }

    /**
     * @param User $receivedBy
     */
    public function setReceivedBy($receivedBy)
    {
        $this->receivedBy = $receivedBy;
    }

    /**
     * @return \DateTime
     */
    public function getReceivedAt()
    {
        return $this->receivedAt;
    }

    /**
     * @param \DateTime $receivedAt
     */
    public function setReceivedAt($receivedAt)
    {
        $this->receivedAt = $receivedAt;
    }

    /**
     * @return Order
     */
    public function getOrderRef()
    {
        return $this->orderRef;
    }

    /**
     * @param Order $order
     */
    public function setOrderRef($order)
    {
        $this->orderRef = $order;
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
     * @return string
     */
    public function getDepositor()
    {
        return $this->depositor;
    }

    /**
     * @param string $depositor
     */
    public function setDepositor($depositor)
    {
        $this->depositor = $depositor;
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
}