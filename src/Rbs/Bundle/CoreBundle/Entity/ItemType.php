<?php
namespace Rbs\Bundle\CoreBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints AS Assert;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * ItemType
 *
 * @ORM\Table(name="core_item_types")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\ItemTypeRepository")
 * @UniqueEntity("itemType")
 * @ORMSubscribedEvents()
 */
class ItemType
{
    use ORMBehaviors\Timestampable\Timestampable,
        ORMBehaviors\SoftDeletable\SoftDeletable,
        ORMBehaviors\Blameable\Blameable;

    const Chick = 'Chick';
    const Fish = 'Fish';
    const Poultry = 'Poultry';
    const Cattle = 'Cattle';

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
     * @ORM\Column(name="item_types", type="string", length=255, columnDefinition="ENUM('Chick', 'Fish', 'Poultry', 'Cattle')")
     * @Assert\NotBlank()
     */
    private $itemType;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\Item", mappedBy="itemType")
     */
    private $item;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\Vendor", mappedBy="itemTypes")
     */
    private $vendors;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 1;

    /**
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\Bundle")
     * @ORM\JoinTable(name="core_join_item_types_bundles")
     * @Assert\NotBlank()
     * @Assert\Count(min = 1, minMessage = "Please select at least {{ limit }} module")
     **/
    private $bundles;

    public function __construct()
    {
        $this->vendors = new ArrayCollection();
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

    public function __toString()
    {
        return $this->getItemType();
    }

    /**
     * Set itemType
     *
     * @param string $itemType
     * @return ItemType
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Get itemType
     *
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return ItemType
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

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return ArrayCollection
     */
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * @param ItemType $vendor
     * @return $this
     */
    public function addVendor(Vendor $vendor)
    {
        var_dump($vendor);exit;
        if (!$this->getVendors()->contains($vendor)) {
            $this->vendors->add($vendor);
        }

        return $this;
    }

    /**
     * @param Vendor $vendor
     */
    public function removeVendor(Vendor $vendor)
    {
        $this->vendors->removeElement($vendor);
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
}