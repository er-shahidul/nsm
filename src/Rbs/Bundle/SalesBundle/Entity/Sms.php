<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Sms
 *
 * @ORM\Table(name="sales_sms")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\SmsRepository")
 * @ORMSubscribedEvents()
 */
class Sms
{
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
     * @var Order
     *
     * @ORM\OneToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Order", inversedBy="refSMS")
     * @ORM\JoinColumn(name="order_id", nullable=true, onDelete="SET NULL")
     */
    private $order;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Agent")
     * @ORM\JoinColumn(name="agents", nullable=true)
     */
    private $agent;

    /**
     * @var string
     *
     * @ORM\Column(name="sl", type="string", length=20)
     */
    private $sl;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_no", type="string", length=255)
     */
    private $mobileNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="msg", type="string", length=160)
     */
    private $msg;

    /**
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('NEW', 'READ', 'UNREAD')", nullable=true)
     */
    private $status;

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

    public function __toString()
    {
        return (string)$this->getId();
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
     */
    public function setOrder($order)
    {
        $this->order = $order;
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getSl()
    {
        return $this->sl;
    }

    /**
     * @param string $sl
     *
     * @return Sms
     */
    public function setSl($sl)
    {
        $this->sl = $sl;

        return $this;
    }

    /**
     * @return string
     */
    public function getMobileNo()
    {
        return $this->mobileNo;
    }

    /**
     * @param string $mobileNo
     *
     * @return Sms
     */
    public function setMobileNo($mobileNo)
    {
        $this->mobileNo = $mobileNo;

        return $this;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     *
     * @return Sms
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;

        return $this;
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
     *
     * @return Sms
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    public function getMobileNoAndMsg()
    {
        return $this->mobileNo . ": " . strip_tags($this->msg);
    }

}