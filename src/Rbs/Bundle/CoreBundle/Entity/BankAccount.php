<?php

namespace Rbs\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BankAccount
 *
 * @ORM\Table(name="core_bank_accounts")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\BankAccountRepository")
 * @UniqueEntity("code")
 * @UniqueEntity(
 *     fields={"name", "branch"},
 *     errorPath="duplicate_account",
 *     message="Account name already exists under given Bank/Branch"
 * )
 */
class BankAccount
{
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
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var BankBranch
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\BankBranch")
     * @ORM\JoinColumn(name="bank_branch_id")
     */
    private $branch;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Rbs\Bundle\SalesBundle\Entity\Payment", mappedBy="bankAccount")
     */
    private $payments;

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
     * @return BankAccount
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
     * Set code
     *
     * @param string $code
     * @return BankAccount
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set branch
     *
     * @param BankBranch $branch
     * @return BankAccount
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return BankBranch
     */
    public function getBranch()
    {
        return $this->branch;
    }


    /**
     * @return ArrayCollection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    public function getBankBranch()
    {
        return "Account: ".$this->getName()." ".$this->branch->getBank()->getName().", ".$this->branch->getName().", Code: ".$this->code;
    }
}