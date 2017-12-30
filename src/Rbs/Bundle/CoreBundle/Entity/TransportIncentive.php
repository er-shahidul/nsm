<?php
namespace Rbs\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * TransportIncentive
 *
 * @ORM\Table(name="core_transport_incentives")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\TransportIncentiveRepository")
 * @ORMSubscribedEvents()
 */
class TransportIncentive
{
    const PER_DELIVERY = 'PER_DELIVERY';

    const CURRENT = 'CURRENT';
    const ARCHIVED = 'ARCHIVED';

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
     * @var ItemType
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\ItemType")
     * @ORM\JoinColumn(name="item_type_id")
     */
    private $itemType;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Location")
     * @ORM\JoinColumn(name="district_id")
     */
    private $district;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Location")
     * @ORM\JoinColumn(name="station_id")
     */
    private $station;

    /**
     * @var Depo
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Depo")
     * @ORM\JoinColumn(name="depo_id")
     */
    private $depo;

    /**
     * @var float
     *
     * @ORM\Column(name="amounts", type="float", nullable=false)
     */
    private $amount;

    /**
     * @var array $type
     *
     * @ORM\Column(name="duration_type", type="string", length=255, columnDefinition="ENUM('PER_DELIVERY')", nullable=false)
     */
    private $durationType = "PER_DELIVERY";

    /**
     * @var integer
     *
     * @ORM\Column(name="quantities", type="integer", nullable=false)
     */
    private $quantity = 1;

    /**
     * @var array $type
     *
     * @ORM\Column(name="type", type="string", length=255, columnDefinition="ENUM('SALE', 'TRANSPORT')", nullable=false)
     */
    private $type = 'TRANSPORT';

    /**
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('CURRENT', 'ARCHIVED')", nullable=false)
     */
    private $status = 'CURRENT';
    
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
     * @return ItemType
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @param ItemType $itemType
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }

    /**
     * @return Location
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param Location $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return Location
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * @param Location $station
     */
    public function setStation($station)
    {
        $this->station = $station;
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
     * @return array
     */
    public function getDurationType()
    {
        return $this->durationType;
    }

    /**
     * @param array $durationType
     */
    public function setDurationType($durationType)
    {
        $this->durationType = $durationType;
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
}