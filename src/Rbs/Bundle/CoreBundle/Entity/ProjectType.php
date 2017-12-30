<?php
namespace Rbs\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * Project
 *
 * @ORM\Table(name="core_project_types")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\ProjectTypeRepository")
 * @ORMSubscribedEvents()
 * @UniqueEntity("name")
 */
class ProjectType
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
        return $this->getName();
    }

    /**
     * Set projectCategoryName
     *
     * @param string $projectCategoryName
     * @return ProjectType
     */
    public function setProjectCategoryName($projectCategoryName)
    {
        $this->name = $projectCategoryName;

        return $this;
    }

    /**
     * Get projectCategoryName
     *
     * @return string
     */
    public function getProjectCategoryName()
    {
        return $this->getName();
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return ProjectType
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

}