<?php
namespace Rbs\Bundle\CoreBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * Vendor
 *
 * @ORM\Table(name="core_vendors")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\VendorRepository")
 * @ORMSubscribedEvents()
 */
class Vendor
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\VendorAttach", mappedBy="vendor", cascade={"persist", "remove"})
     */
    private $vendorAttach;

    /**
     * @var string
     *
     * @ORM\Column(name="vendors_name", type="string", length=255)
     */
    private $vendorName;

    /**
     * @var string
     *
     * @ORM\Column(name="vendors_address", type="text")
     */
    private $vendorAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_person", type="string", length=255)
     */
    private $contractPerson;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_no", type="string", length=255)
     */
    private $contractNo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="trade_license_no", type="string", length=255)
     */
    private $tradeLicenseNo;

    /**
     * @var string
     *
     * @ORM\Column(name="tin_certificate_no", type="string", length=255)
     */
    private $tinCertificateNo;

    /**
     * @var string
     *
     * @ORM\Column(name="vat_certificate_no", type="string", length=255)
     */
    private $vatCertificateNo;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_no", type="string", length=255)
     */
    private $bankAccountNo;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_name", type="string", length=255)
     */
    private $bankAccountName;

    /**
     * @var string
     *
     * @ORM\Column(name="branch_name", type="string", length=255)
     */
    private $branchName;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type", type="string", length=255)
     */
    private $PaymentType;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Rbs\Bundle\CoreBundle\Entity\ItemType", inversedBy="vendors", cascade={"persist"})
     * @ORM\JoinTable(name="core_join_vendors_item_types")
     */
    protected $itemTypes;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 1;

    public function __construct()
    {
        $this->itemTypes = new ArrayCollection();
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
        return $this->getVendorName();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getVendorAttach()
    {
        return $this->vendorAttach;
    }

    /**
     * Set vendorName
     *
     * @param string $vendorName
     * @return Vendor
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;

        return $this;
    }

    /**
     * Get vendorName
     *
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * Set contractPerson
     *
     * @param string $contractPerson
     * @return Vendor
     */
    public function setContractPerson($contractPerson)
    {
        $this->contractPerson = $contractPerson;

        return $this;
    }

    /**
     * Get contractPerson
     *
     * @return string
     */
    public function getContractPerson()
    {
        return $this->contractPerson;
    }

    /**
     * Set contractNo
     *
     * @param string $contractNo
     * @return Vendor
     */
    public function setContractNo($contractNo)
    {
        $this->contractNo = $contractNo;

        return $this;
    }

    /**
     * Get contractNo
     *
     * @return string
     */
    public function getContractNo()
    {
        return $this->contractNo;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Vendor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set tradeLicenseNo
     *
     * @param string $tradeLicenseNo
     * @return Vendor
     */
    public function setTradeLicenseNo($tradeLicenseNo)
    {
        $this->tradeLicenseNo = $tradeLicenseNo;

        return $this;
    }

    /**
     * Get tradeLicenseNo
     *
     * @return string
     */
    public function getTradeLicenseNo()
    {
        return $this->tradeLicenseNo;
    }

    /**
     * Set tinCertificateNo
     *
     * @param string $tinCertificateNo
     * @return Vendor
     */
    public function setTinCertificateNo($tinCertificateNo)
    {
        $this->tinCertificateNo = $tinCertificateNo;

        return $this;
    }

    /**
     * Get tinCertificateNo
     *
     * @return string
     */
    public function getTinCertificateNo()
    {
        return $this->tinCertificateNo;
    }

    /**
     * Set vatCertificateNo
     *
     * @param string $vatCertificateNo
     * @return Vendor
     */
    public function setVatCertificateNo($vatCertificateNo)
    {
        $this->vatCertificateNo = $vatCertificateNo;

        return $this;
    }

    /**
     * Get vatCertificateNo
     *
     * @return string
     */
    public function getVatCertificateNo()
    {
        return $this->vatCertificateNo;
    }

    /**
     * Set bankAccountNo
     *
     * @param string $bankAccountNo
     * @return Vendor
     */
    public function setBankAccountNo($bankAccountNo)
    {
        $this->bankAccountNo = $bankAccountNo;

        return $this;
    }

    /**
     * Get bankAccountNo
     *
     * @return string
     */
    public function getBankAccountNo()
    {
        return $this->bankAccountNo;
    }

    /**
     * Set bankAccountName
     *
     * @param string $bankAccountName
     * @return Vendor
     */
    public function setBankAccountName($bankAccountName)
    {
        $this->bankAccountName = $bankAccountName;

        return $this;
    }

    /**
     * Get bankAccountName
     *
     * @return string
     */
    public function getBankAccountName()
    {
        return $this->bankAccountName;
    }

    /**
     * Set branchName
     *
     * @param string $branchName
     * @return Vendor
     */
    public function setBranchName($branchName)
    {
        $this->branchName = $branchName;

        return $this;
    }

    /**
     * Get branchName
     *
     * @return string
     */
    public function getBranchName()
    {
        return $this->branchName;
    }

    /**
     * Get vendorAddress
     * @return string
     */
    public function getVendorAddress()
    {
        return $this->vendorAddress;
    }

    /**
     * Set vendorAddress
     *
     * @param string $vendorAddress
     * @return Vendor
     */
    public function setVendorAddress($vendorAddress)
    {
        $this->vendorAddress = $vendorAddress;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Vendor
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
    public function getPaymentType()
    {
        return $this->PaymentType;
    }

    /**
     * @param string $PaymentType
     */
    public function setPaymentType($PaymentType)
    {
        $this->PaymentType = $PaymentType;
    }

    /**
     * @return ArrayCollection
     */
    public function getItemTypes()
    {
        return $this->itemTypes;
    }

    /**
     * @param ItemType $itemType
     * @return $this
     */
    public function addItemType(ItemType $itemType)
    {
        if (!$this->getItemTypes()->contains($itemType)) {
            $this->itemTypes->add($itemType);
        }

        return $this;
    }

    /**
     * @param ItemType $itemType
     */
    public function removeBundle(ItemType $itemType)
    {
        $this->itemTypes->removeElement($itemType);
    }
}