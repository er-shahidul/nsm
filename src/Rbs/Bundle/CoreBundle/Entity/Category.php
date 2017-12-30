<?php
namespace Rbs\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="core_categories")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\CategoryRepository")
 * @UniqueEntity("name")
 * @ORMSubscribedEvents()
 */
class Category
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
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\Item", mappedBy="category")
     **/
    private $item;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 1;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\SubCategory", mappedBy="category", cascade={"persist", "remove"})
     */
    private $subCategories;

    /**
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\Bundle")
     * @ORM\JoinTable(name="core_join_categories_bundles")
     * @Assert\NotBlank()
     * @Assert\Count(min = 1, minMessage = "Please select at least {{ limit }} module")
     **/
    private $bundles;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
        $this->item = new ArrayCollection();
        $this->bundles = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function addSubCategory(SubCategory $subCategory)
    {
        $subCategory->setCategory($this);

        if (!$this->getSubCategories()->contains($subCategory)) {
            $this->subCategories->add($subCategory);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory)
    {
        if ($this->getSubCategories()->contains($subCategory)) {
            $this->getSubCategories()->removeElement($subCategory);
        }
    }

    public function getSubCategories()
    {
        return $this->subCategories;
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
     * Set categoryName
     *
     * @param string $categoryName
     * @return Category
     */
    public function setCategoryName($categoryName)
    {
        $this->name = $categoryName;

        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getName();
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Category
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

    public function addItem(Item $item)
    {
        $item->addCategory($this);

        if (!$this->getItem()->contains($item)) {
            $this->item->add($item);
        }

        return $this;
    }

    public function removeItem(Item $item)
    {
        if ($this->getItem()->contains($item)) {
            $this->getItem()->removeElement($item);
        }
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
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
     */
    public function setName($name)
    {
        $this->name = $name;
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