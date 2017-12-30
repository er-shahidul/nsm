<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\BankAccount;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Payment
 *
 * @ORM\Table(name="sales_payments")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\PaymentRepository")
 * @ORMSubscribedEvents()
 */
class Payment
{
    const PAYMENT_METHOD_CASH = 'CASH';
    const PAYMENT_METHOD_REFUND = 'REFUND';
    const PAYMENT_METHOD_CHEQUE = 'CHEQUE';
    const PAYMENT_METHOD_BANK = 'BANK';
    const PAYMENT_METHOD_INCENTIVE = 'INCENTIVE';
    const PAYMENT_METHOD_OPENING_BALANCE = 'OPENING_BALANCE';

    const DR = 'DR';
    const CR = 'CR';

    use ORMBehaviors\Timestampable\Timestampable,
        //ORMBehaviors\SoftDeletable\SoftDeletable,
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
     * @ORM\ManyToMany(targetEntity="Order", inversedBy="payments")
     * @ORM\JoinTable(name="sales_join_payments_orders",
     *      joinColumns={@ORM\JoinColumn(name="payment_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")}
     * )
     */
    protected $orders;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Agent", inversedBy="payments", cascade={"persist"})
     * @ORM\JoinColumn(name="agent_id", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $agent;

    /**
     * @var array $type
     *
     * @ORM\Column(name="payment_method", type="string", length=255, columnDefinition="ENUM('CASH', 'CHEQUE', 'BANK', 'REFUND', 'INCENTIVE', 'OPENING_BALANCE')", nullable=false)
     */
    private $paymentMethod = 'BANK';

    /**
     * @var array $type
     *
     * @ORM\Column(name="transaction_type", type="string", length=255, columnDefinition="ENUM('DR', 'CR')", nullable=true)
     */
    private $transactionType;

    /**
     * @var BankAccount
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\BankAccount", inversedBy="payments", cascade={"persist"})
     * @ORM\JoinColumn(name="bank_account_id", nullable=true)
     *
     */
    private $bankAccount;

    /**
     * @var float
     *
     * @ORM\Column(name="amounts", type="float", nullable=false)
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="deposited_amounts", type="float", nullable=false)
     * @Assert\NotBlank()
     */
    private $depositedAmount =0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deposit_date", type="datetime", nullable=false)
     * @Assert\NotBlank()
     */
    private $depositDate;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", nullable=true)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_via", type="string", length=6, nullable=true)
     */
    private $paymentVia = 'SYSTEM';

    /**
     * @var boolean
     *
     * @ORM\Column(name="verified", type="boolean", nullable=true)
     */
    private $verified = false;

    /**
     * @var string
     *
     * @ORM\Column(name="fx_cx", type="string", length=6, nullable=true)
     */
    private $fxCx;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_bank", type="string", length=255, nullable=true)
     */
    private $agentBank = '';

    /**
     * @var string
     *
     * @ORM\Column(name="agent_branch", type="string", length=255, nullable=true)
     */
    private $agentBranch = '';

    /**
     * @var AgentBank
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\AgentBank", inversedBy="payments", cascade={"persist"})
     * @ORM\JoinColumn(name="agent_bank_branch_id", nullable=true)
     **/
    private $agentBankBranch;

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
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return array
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     * @param \Rbs\Bundle\SalesBundle\Entity\Order $order
     */
    public function addOrder($order)
    {
        if (!$this->getOrders()->contains($order)) {
            $this->getOrders()->add($order);
        }
    }

    /**
     * @param \Rbs\Bundle\SalesBundle\Entity\Order $order
     */
    public function removeOrder($order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * @return string
     */
    public function getPaymentVia()
    {
        return $this->paymentVia;
    }

    /**
     * @param string $paymentVia
     *
     * @return Payment
     */
    public function setPaymentVia($paymentVia)
    {
        $this->paymentVia = $paymentVia;

        return $this;
    }

    /**
     * @return array
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     * @return boolean
     */
    public function isVerified()
    {
        return $this->verified;
    }

    /**
     * @param boolean $verified
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
    }

    public function isVerifiedTrue()
    {
        if($this->verified == true){
            return 'VERIFIED';
        }
        return 'NOT VERIFIED';
    }

    /**
     * @return BankAccount
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * @param $bankAccount
     *
     * @return Payment
     */
    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * @return AgentBank
     */
    public function getAgentBankBranch()
    {
        return $this->agentBankBranch;
    }

    /**
     * @param $agentBankBranch
     *
     * @return Payment
     */
    public function setAgentBankBranch($agentBankBranch)
    {
        $this->agentBankBranch = $agentBankBranch;

        return $this;
    }

    /**
     * @return string
     */
    public function getAgentBank()
    {
        return $this->agentBank;
    }

    /**
     * @param string $agentBank
     *
     * @return Payment
     */
    public function setAgentBank($agentBank)
    {
        $this->agentBank = $agentBank;

        return $this;
    }

    /**
     * @return string
     */
    public function getAgentBranch()
    {
        return $this->agentBranch;
    }

    /**
     * @param string $agentBranch
     *
     * @return Payment
     */
    public function setAgentBranch($agentBranch)
    {
        $this->agentBranch = $agentBranch;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDepositDate()
    {
        return $this->depositDate;
    }

    /**
     * @param \DateTime $depositDate
     */
    public function setDepositDate($depositDate)
    {
        $this->depositDate = $depositDate = false ? "null" : new \DateTime($depositDate);
    }

    /**
     * @return float
     */
    public function getDepositedAmount()
    {
        return $this->depositedAmount;
    }

    /**
     * @param float $depositedAmount
     */
    public function setDepositedAmount($depositedAmount)
    {
        $this->depositedAmount = $depositedAmount;
    }

    /**
     * @return string
     */
    public function getFxCx()
    {
        return $this->fxCx;
    }

    /**
     * @param string $fxCx
     */
    public function setFxCx($fxCx)
    {
        $this->fxCx = $fxCx;
    }
}