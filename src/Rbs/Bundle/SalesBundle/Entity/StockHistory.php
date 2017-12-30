<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * StockHistory
 *
 * @ORM\Table(name="sales_stock_histories")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\StockHistoryRepository")
 * @ORMSubscribedEvents()
 */
class StockHistory
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
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var Stock
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Stock", inversedBy="stockHistories")
     * @ORM\JoinColumn(name="stock_id")
     */
    private $stock;

    /**
     * @var FeedMill
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\FeedMill", inversedBy="stockHistories")
     * @ORM\JoinColumn(name="feed_mill_id")
     */
    private $feedMill;

    /**
     * @var Depo
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Depo")
     * @ORM\JoinColumn(name="from_depo", nullable=false)
     */
    private $fromDepo;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * @return Stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param Stock $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return FeedMill
     */
    public function getFeedMill()
    {
        return $this->feedMill;
    }

    /**
     * @param FeedMill $feedMill
     */
    public function setFeedMill($feedMill)
    {
        $this->feedMill = $feedMill;
    }

    /**
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return Depo
     */
    public function getFromDepo()
    {
        return $this->fromDepo;
    }

    /**
     * @param Depo $fromDepo
     */
    public function setFromDepo($fromDepo)
    {
        $this->fromDepo = $fromDepo;
    }
}