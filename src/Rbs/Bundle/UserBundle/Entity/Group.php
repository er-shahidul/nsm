<?php

namespace Rbs\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * @ORM\Table(name="user_groups")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\UserBundle\Repository\GroupRepository")
 * @ORMSubscribedEvents()
 * @UniqueEntity(
 *     fields={"name"},
 *     message="This group name is already in use."
 * )
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     **/
    protected $users;

    public function __construct($name, $roles = array())
    {
        parent::__construct($name);
        $this->users = new ArrayCollection();
    }

    /**
     * Set description
     *
     * @param mixed $description
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }
}