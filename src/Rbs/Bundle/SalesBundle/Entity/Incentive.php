<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Incentive
 *
 * @ORM\Table(name="sales_incentives")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\IncentiveRepository")
 * @ORMSubscribedEvents()
 */
class Incentive
{
    const DENIED = 'DENIED';
    const ACTIVE = 'ACTIVE';
    const PENDING = 'PENDING';
    const APPROVED = 'APPROVED';

    const YEAR = 'YEAR';
    const MONTH = 'MONTH';
    const SALE = 'SALE';
    const TRANSPORT = 'TRANSPORT';
    const TT = 'TT';
    const DT = 'DT';

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
     * @ORM\JoinColumn(name="agents", nullable=true)
     */
    private $agent;

    /**
     * @var array $type
     *
     * @ORM\Column(name="duration", type="string", length=255, columnDefinition="ENUM('MONTH', 'YEAR')", nullable=false)
     */
    private $duration;

    /**
     * @var array $type
     *
     * @ORM\Column(name="type", type="string", length=255, columnDefinition="ENUM('SALE', 'TRANSPORT', 'TT', 'DT')", nullable=false)
     */
    private $type;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dates", type="datetime", nullable=true)
     */
    private $date;
    
    /**
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('ACTIVE', 'DENIED', 'APPROVED', 'PENDING')", nullable=false)
     */
    private $status = 'PENDING';

    /**
     * @var float
     *
     * @ORM\Column(name="amounts", type="float", nullable=false)
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $details;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="approved_by")
     */
    private $approvedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;
    
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
     * @return array
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param array $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param string $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * @return User
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param User $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return \DateTime
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * @param \DateTime $approvedAt
     */
    public function setApprovedAt($approvedAt)
    {
        $this->approvedAt = $approvedAt;
    }

    public function isActive()
    {
        $state = false;
        if($this->getStatus() == Incentive::PENDING){
            $state = true;
        }

        return $state;
    }
}