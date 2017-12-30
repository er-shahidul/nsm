<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\Category;
use Rbs\Bundle\CoreBundle\Entity\Location;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Target
 *
 * @ORM\Table(name="sales_targets")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\TargetRepository")
 * @ORMSubscribedEvents()
 */
class Target
{
    const RUNNING = 'RUNNING';
    const PENDING = 'PENDING';
    const CLOSED = 'CLOSED';
    const INACTIVE = 'INACTIVE';
    const ACTIVE = 'ACTIVE';

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
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="remaining", type="integer", nullable=true)
     */
    private $remaining = 0;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Location")
     * @ORM\JoinColumn(name="zilla_id", nullable=true)
     */
    private $zilla;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Location")
     * @ORM\JoinColumn(name="upozilla_id", nullable=true)
     */
    private $upozilla;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id")
     */
    private $category;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('RUNNING', 'PENDING' , 'CLOSED', 'ACTIVE', 'INACTIVE')", nullable=false)
     */
    private $status = 'RUNNING';

    public $childEntities;

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
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * @param int $remaining
     */
    public function setRemaining($remaining)
    {
        $this->remaining = $remaining;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return Location
     */
    public function getZilla()
    {
        return $this->zilla;
    }

    /**
     * @param Location $zilla
     */
    public function setZilla($zilla)
    {
        $this->zilla = $zilla;
    }

    /**
     * @return Location
     */
    public function getUpozilla()
    {
        return $this->upozilla;
    }

    /**
     * @param Location $upozilla
     */
    public function setUpozilla($upozilla)
    {
        $this->upozilla = $upozilla;
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
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getChildEntities()
    {
        return $this->childEntities;
    }

    /**
     * @param mixed $childEntities
     */
    public function setChildEntities($childEntities)
    {
        $this->childEntities = $childEntities;
    }

    /**
     * @return mixed
     */
    public function getMonthDifference()
    {
        $difference = $this->getStartDate()->diff($this->getEndDate()); // $difference->y // $difference->m // $difference->d
        
        return $difference->m + 1 ;
    }
}