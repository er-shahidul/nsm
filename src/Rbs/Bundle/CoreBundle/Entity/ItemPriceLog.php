<?php
namespace Rbs\Bundle\CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * ItemPriceLog
 *
 * @ORM\Table(name="core_items_price_log")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\ItemPriceLogRepository")
 * @ORMSubscribedEvents()
 */
class ItemPriceLog
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
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Item")
     * @ORM\JoinColumn(name="items")
     */
    private $item;

    /**
     * @var float
     *
     * @ORM\Column(name="previous_prices", type="float")
     * @Assert\NotBlank()
     */
    private $previousPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="current_prices", type="float")
     * @Assert\NotBlank()
     */
    private $currentPrice;

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
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return float
     */
    public function getPreviousPrice()
    {
        return $this->previousPrice;
    }

    /**
     * @param float $previousPrice
     */
    public function setPreviousPrice($previousPrice)
    {
        $this->previousPrice = $previousPrice;
    }

    /**
     * @return float
     */
    public function getCurrentPrice()
    {
        return $this->currentPrice;
    }

    /**
     * @param float $currentPrice
     */
    public function setCurrentPrice($currentPrice)
    {
        $this->currentPrice = $currentPrice;
    }
}