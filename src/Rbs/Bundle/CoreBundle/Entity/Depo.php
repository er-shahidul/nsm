<?php

namespace Rbs\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * Depo
 *
 * @ORM\Table(name="core_depos")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\DepoRepository")
 * @ORMSubscribedEvents()
 * @UniqueEntity("name")
 */
class Depo
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
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="Location")
     **/
    private $location;

    /**
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="core_join_users_depos")
     * @Assert\NotBlank()
     **/
    private $users;

    /**
     * @var boolean
     *
     * @ORM\Column(name="used_in_transport", type="boolean", nullable=true)
     */
    private $usedInTransport = false;

    public function __construct()
    {
        $this->bundles = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Depo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Depo
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user)
    {
        if (!$this->getUsers()->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return boolean
     */
    public function isUsedInTransport()
    {
        return $this->usedInTransport;
    }

    /**
     * @param boolean $usedInTransport
     */
    public function setUsedInTransport($usedInTransport)
    {
        $this->usedInTransport = $usedInTransport;
    }

    public static function depoIdNameFormat($id, $name)
    {
        return $id . ' - ' . $name;
    }
}
