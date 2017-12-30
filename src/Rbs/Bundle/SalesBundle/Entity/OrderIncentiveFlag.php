<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderIncentiveFlag
 *
 * @ORM\Table(name="sales_order_incentive_flag")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\OrderIncentiveFlagRepository")
 */
class OrderIncentiveFlag
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
     * @ORM\OneToOne(targetEntity="Order", inversedBy="orderIncentiveFlag")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     */
    protected $order;

    /**
     * @var boolean
     *
     * @ORM\Column(name="month_flag", type="boolean", nullable=true)
     */
    private $monthFlag = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="year_flag", type="boolean", nullable=true)
     */
    private $yearFlag = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_id", type="integer", nullable=true)
     */
    private $incentiveId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="incentive_date", type="datetime", nullable=true)
     */
    private $incentiveDate;

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
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return boolean
     */
    public function isMonthFlag()
    {
        return $this->monthFlag;
    }

    /**
     * @param boolean $monthFlag
     */
    public function setMonthFlag($monthFlag)
    {
        $this->monthFlag = $monthFlag;
    }

    /**
     * @return boolean
     */
    public function isYearFlag()
    {
        return $this->yearFlag;
    }

    /**
     * @param boolean $yearFlag
     */
    public function setYearFlag($yearFlag)
    {
        $this->yearFlag = $yearFlag;
    }

    /**
     * @return int
     */
    public function getIncentiveId()
    {
        return $this->incentiveId;
    }

    /**
     * @param int $incentiveId
     */
    public function setIncentiveId($incentiveId)
    {
        $this->incentiveId = $incentiveId;
    }

    /**
     * @return \DateTime
     */
    public function getIncentiveDate()
    {
        return $this->incentiveDate;
    }

    /**
     * @param \DateTime $incentiveDate
     */
    public function setIncentiveDate($incentiveDate)
    {
        $this->incentiveDate = $incentiveDate;
    }
}