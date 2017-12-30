<?php
namespace Rbs\Bundle\CoreBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Item
 *
 * @ORM\Table(name="core_items")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\ItemRepository")
 * @UniqueEntity("name")
 * @ORMSubscribedEvents()
 */
class Item
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
     * @var Category
     *
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\Category", inversedBy="item")
     * @ORM\JoinTable(name="core_join_items_categories",
     *      joinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="categories_id", referencedColumnName="id")}
     * )
     * @ORM\JoinColumn(name="categories")
     * @Assert\NotBlank()
     * @Assert\Count(min = 1, minMessage = "Please select a category")
     * @Assert\Count(max = 1, maxMessage = "Please select a category only")
     */
    private $category;

    /**
     * @var ItemType
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\ItemType", inversedBy="item")
     * @ORM\JoinColumn(name="item_types")
     */
    private $itemType;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="item_unit", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $itemUnit;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="prices", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="due_amount", type="float", nullable=true)
     */
    private $dueAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="SKU", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $sku;

    /**
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\Bundle")
     * @ORM\JoinTable(name="core_join_items_bundles")
     * @Assert\NotBlank()
     * @Assert\Count(min = 1, minMessage = "Please select at least {{ limit }} module")
     **/
    private $bundles;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->bundles = new ArrayCollection();
    }

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
     * Set itemName
     *
     * @param string $itemName
     * @return Item
     */
    public function setItemName($itemName)
    {
        $this->name = $itemName;

        return $this;
    }

    /**
     * Get itemName
     *
     * @return string
     */
    public function getItemName()
    {
        return $this->getName();
    }

    public function addCategory(Category $category)
    {
        $category->addItem($this);

        if (!$this->getCategory()->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category)
    {
        if ($this->getCategory()->contains($category)) {
            $this->getCategory()->removeElement($category);
        }
    }

    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set itemType
     *
     * @param ItemType $itemType
     * @return $this
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Get itemType
     *
     * @return ItemType
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Set itemUnit
     *
     * @param string $itemUnit
     * @return $this
     */
    public function setItemUnit($itemUnit)
    {
        $this->itemUnit = $itemUnit;

        return $this;
    }

    /**
     * Get itemUnit
     *
     * @return string
     */
    public function getItemUnit()
    {
        return $this->itemUnit;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Add bundle
     *
     * @param Bundle $bundle
     * @return $this
     */
    public function addBundle(Bundle $bundle)
    {
        if (!$this->getBundles()->contains($bundle)) {
            $this->bundles->add($bundle);
        }

        return $this;
    }

    /**
     * Remove bundle
     *
     * @param Bundle $bundle
     */
    public function removeBundle(Bundle $bundle)
    {
        $this->bundles->removeElement($bundle);
    }

    /**
     * Get bundle
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $SKU
     */
    public function setSku($SKU)
    {
        $this->sku = $SKU;
    }

    /**
     * @return float
     */
    public function getDueAmount()
    {
        return $this->dueAmount;
    }

    /**
     * @param float $dueAmount
     */
    public function setDueAmount($dueAmount)
    {
        $this->dueAmount = $dueAmount;
    }

    public static function itemCodeNameFormat($code, $name)
    {
        return $code . ' - ' . $name;
    }

    public function getItemCodeName()
    {
        return Item::itemCodeNameFormat($this->getSku(), $this->getName());
    }

    /**
     * @return Category
     */
    public function getFirstCategory()
    {
        return $this->category[0];
    }

    public function getItemInfo()
    {
        return $this->getSku() ." - ".$this->getName();
    }
}