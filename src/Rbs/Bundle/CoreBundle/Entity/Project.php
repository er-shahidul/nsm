<?php
namespace Rbs\Bundle\CoreBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\UserBundle\Entity\User;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * Project
 *
 * @ORM\Table(name="core_projects")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\ProjectRepository")
 * @ORMSubscribedEvents()
 * @UniqueEntity("projectName")
 */
class Project
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
     * @ORM\Column(name="projects_name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $projectName;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_center_number", type="string", length=255, nullable=true)
     */
    private $costCenterNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 1;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="project_heads", nullable=true)
     */
    private $projectHead;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="project_contact_persons", nullable=true)
     */
    private $projectContactPerson;

    /**
     * @var ProjectType
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\ProjectType")
     * @ORM\JoinColumn(name="project_types", nullable=true)
     */
    private $projectCategory;

    /**
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\UserBundle\Entity\User", mappedBy="projects")
     **/
    protected $users;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_head_office", type="boolean")
     */
    private $isHeadOffice = false;

    /**
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\Bundle")
     * @ORM\JoinTable(name="core_join_projects_bundles")
     * @Assert\NotBlank()
     * @Assert\Count(min = 1, minMessage = "Please select at least {{ limit }} module")
     **/
    private $bundles;

    public function __construct()
    {
        $this->bundles = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function addUser(User $user)
    {
        $user->addProject($this);

        if (!$this->getUsers()->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user)
    {
        if ($this->getUsers()->contains($user)) {
            $this->getUsers()->removeElement($user);
        }
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
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
     * Set projectHead
     *
     * @param User $projectHead
     * @return Project
     */
    public function setProjectHead($projectHead)
    {
        $this->projectHead = $projectHead;

        return $this;
    }

    /**
     * Get projectHead
     *
     * @return User
     */
    public function getProjectHead()
    {
        return $this->projectHead;
    }

    /**
     * Set projectContactPerson
     *
     * @param User $projectContactPerson
     * @return Project
     */
    public function setProjectContactPerson($projectContactPerson)
    {
        $this->projectContactPerson = $projectContactPerson;

        return $this;
    }

    /**
     * Get projectContactPerson
     *
     * @return User
     */
    public function getProjectContactPerson()
    {
        return $this->projectContactPerson;
    }

    /**
     * Set projectCategory
     *
     * @param ProjectType $projectCategory
     * @return Project
     */
    public function setProjectCategory($projectCategory)
    {
        $this->projectCategory = $projectCategory;

        return $this;
    }

    /**
     * Get projectCategory
     *
     * @return ProjectType
     */
    public function getProjectCategory()
    {
        return $this->projectCategory;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Project
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set costCenterNumber
     *
     * @param string $costCenterNumber
     * @return Project
     */
    public function setCostCenterNumber($costCenterNumber)
    {
        $this->costCenterNumber = $costCenterNumber;

        return $this;
    }

    /**
     * Get costCenterNumber
     *
     * @return string
     */
    public function getCostCenterNumber()
    {
        return $this->costCenterNumber;
    }

    /**
     * Set projectName
     *
     * @param string $projectName
     * @return Project
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * Get projectName
     *
     * @return User
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Project
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
     * @return boolean
     */
    public function isIsHeadOffice()
    {
        return $this->isHeadOffice;
    }

    /**
     * @param boolean $isHeadOffice
     */
    public function setIsHeadOffice($isHeadOffice)
    {
        $this->isHeadOffice = $isHeadOffice;
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