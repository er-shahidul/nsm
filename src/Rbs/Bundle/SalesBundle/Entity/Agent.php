<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Rbs\Bundle\CoreBundle\Entity\ItemType;
use Rbs\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Agent
 *
 * @ORM\Table(name="sales_agents")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\AgentRepository")
 * @ORMSubscribedEvents()
 * @UniqueEntity("agentID")
 */
class Agent
{
    const DR = 'DR';
    const CR = 'CR';

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
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var AgentGroup
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\AgentGroup", inversedBy="agents")
     * @ORM\JoinColumn(name="agent_group")
     */
    private $agentGroup;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="agent", cascade={"persist"})
     */
    private $payments;

    /**
     * @ORM\OneToMany(targetEntity="AgentDoc", mappedBy="agent", cascade={"persist"})
     */
    private $agentDocs;

    /**
     * @ORM\OneToMany(targetEntity="AgentsBankInfo", mappedBy="agent", cascade={"persist"})
     */
    private $agentsBankInfo;

    /**
     * @var float
     *
     * @ORM\Column(name="opening_balance", type="float", nullable=true)
     */
    private $openingBalance = 0;

    /**
     * @var array $type
     *
     * @ORM\Column(name="opening_balance_type", type="string", length=255, columnDefinition="ENUM('DR', 'CR')", nullable=true)
     */
    private $openingBalanceType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="opening_balance_flag", type="boolean", nullable=true)
     */
    private $openingBalanceFlag = false;
    
    /**
     * @var string
     *
     * @ORM\Column(name="agent_ID", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $agentID;

    /**
     * @var ItemType
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\ItemType")
     * @ORM\JoinColumn(name="item_type", nullable=true)
     */
    private $itemType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vip", type="boolean", nullable=true)
     */
    private $VIP;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="sr", nullable=true)
     */
    private $sr;

    /**
     * @var Depo
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Depo")
     * @ORM\JoinColumn(name="depo", nullable=true)
     * @Assert\NotBlank()
     */
    private $depo;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Rbs\Bundle\SalesBundle\Entity\ChickenSetForAgent", mappedBy="agent")
     */
    private $chickenSetForAgent;
    
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getAgentID()
    {
        return $this->agentID;
    }

    /**
     * @param string $agentID
     */
    public function setAgentID($agentID)
    {
        $this->agentID = $agentID;
    }

    /**
     * @return User
     */
    public function getSr()
    {
        return $this->sr;
    }

    /**
     * @param User $sr
     */
    public function setSr($sr)
    {
        $this->sr = $sr;
    }

    /**
     * @return Depo
     */
    public function getDepo()
    {
        return $this->depo;
    }

    /**
     * @param Depo $depo
     */
    public function setDepo($depo)
    {
        $this->depo = $depo;
    }

    /**
     * @return AgentGroup
     */
    public function getAgentGroup()
    {
        return $this->agentGroup;
    }

    /**
     * @param AgentGroup $agentGroup
     */
    public function setAgentGroup($agentGroup)
    {
        $this->agentGroup = $agentGroup;
    }

    /**
     * @return boolean
     */
    public function isVIP()
    {
        return $this->VIP;
    }

    /**
     * @param boolean $VIP
     */
    public function setVIP($VIP)
    {
        $this->VIP = $VIP;
    }

    /**
     * @return float
     */
    public function getOpeningBalance()
    {
        return $this->openingBalance;
    }

    /**
     * @param float $openingBalance
     */
    public function setOpeningBalance($openingBalance)
    {
        $this->openingBalance = $openingBalance;
    }

    /**
     * @return ItemType
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @param ItemType $itemType
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }

    /**
     * @return mixed
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param mixed $payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    /**
     * @return mixed
     */
    public function getAgentDocs()
    {
        return $this->agentDocs;
    }

    /**
     * @param mixed $agentDocs
     */
    public function setAgentDocs($agentDocs)
    {
        $this->agentDocs = $agentDocs;
    }

    /**
     * @return mixed
     */
    public function getAgentsBankInfo()
    {
        return $this->agentsBankInfo;
    }

    /**
     * @param mixed $agentsBankInfo
     */
    public function setAgentsBankInfo($agentsBankInfo)
    {
        $this->agentsBankInfo = $agentsBankInfo;
    }

    /**
     * @return array
     */
    public function getOpeningBalanceType()
    {
        return $this->openingBalanceType;
    }

    /**
     * @param array $openingBalanceType
     */
    public function setOpeningBalanceType($openingBalanceType)
    {
        $this->openingBalanceType = $openingBalanceType;
    }

    /**
     * @return boolean
     */
    public function isOpeningBalanceFlag()
    {
        return $this->openingBalanceFlag;
    }

    /**
     * @param boolean $openingBalanceFlag
     */
    public function setOpeningBalanceFlag($openingBalanceFlag)
    {
        $this->openingBalanceFlag = $openingBalanceFlag;
    }

    /**
     * @return ArrayCollection
     */
    public function getChickenSetForAgent()
    {
        return $this->chickenSetForAgent;
    }

    /**
     * @param ArrayCollection $chickenSetForAgent
     */
    public function setChickenSetForAgent($chickenSetForAgent)
    {
        $this->chickenSetForAgent = $chickenSetForAgent;
    }

    public function getName()
    {
        return !empty($this->getUser()->getProfile()->getFullName()) 
            ? $this->getUser()->getProfile()->getFullName()
            : $this->getUser()->getUsername();
    }

    public static function agentIdNameFormat($id, $name)
    {
        return $id . ' - ' . $name;
    }

    public function getIdName()
    {
        return Agent::agentIdNameFormat($this->getAgentID(), $this->getUser()->getProfile()->getFullName());
    }
}