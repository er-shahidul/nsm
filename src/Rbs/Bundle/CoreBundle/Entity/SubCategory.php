<?php
namespace Rbs\Bundle\CoreBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\UserBundle\Entity\User;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * SubCategory
 *
 * @ORM\Table(name="core_sub_categories")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\SubCategoryRepository")
 * @ORMSubscribedEvents()
 */
class SubCategory
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $subCategoryName;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 1;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Category", inversedBy="subCategories", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="categories", nullable=true)
     */
    private $category;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="category_heads", nullable=true)
     */
    private $head;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="category_sub_heads", nullable=true)
     */
    private $subHead;

    public function __toString()
    {
        return $this->getSubCategoryName();
    }

    /**
     * Set head
     *
     * @param User $head
     * @return SubCategory
     */
    public function setHead($head)
    {
        $this->head = $head;

        return $this;
    }

    /**
     * Get head
     *
     * @return User
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Set subHead
     *
     * @param User $subHead
     * @return SubCategory
     */
    public function setSubHead($subHead)
    {
        $this->subHead = $subHead;

        return $this;
    }

    /**
     * Get subHead
     *
     * @return User
     */
    public function getSubHead()
    {
        return $this->subHead;
    }

    /**
     * Set category
     *
     * @param Category $category
     * @return SubCategory
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
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
     * @return string
     */
    public function getSubCategoryName()
    {
        return $this->subCategoryName;
    }

    /**
     * @param string $subCategoryName
     */
    public function setSubCategoryName($subCategoryName)
    {
        $this->subCategoryName = $subCategoryName;
    }

    /**
     * Set createdBy
     *
     * @param User $createdBy
     * @return SubCategory
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return SubCategory
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
}