<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * CashDeposit
 *
 * @ORM\Table(name="sales_cash_deposits")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\CashDepositRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORMSubscribedEvents()
 */
class CashDeposit
{
    const ACTIVE = 'ACTIVE';
    const INACTIVE = 'INACTIVE';

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
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('ACTIVE', 'INACTIVE')", nullable=false)
     */
    private $status = 'ACTIVE';

    /**
     * @var float
     *
     * @ORM\Column(name="deposits", type="float", nullable=false)
     * @Assert\NotBlank()
     */
    private $deposit;

    /**
     * @var float
     *
     * @ORM\Column(name="total_deposited_amount", type="float", nullable=false)
     * @Assert\NotBlank()
     */
    private $totalDepositedAmount;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="deposited_by")
     */
    private $depositedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deposited_at", type="datetime", nullable=true)
     */
    private $depositedAt;
    
    /**
     * @var Depo
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\CoreBundle\Entity\Depo")
     * @ORM\JoinColumn(name="depo_id")
     */
    private $depo;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_name", type="string", length=250, nullable=true)
     * @Assert\NotBlank()
     */
    private $bankName;

    /**
     * @var string
     *
     * @ORM\Column(name="branch_name", type="string", length=250, nullable=true)
     * @Assert\NotBlank()
     */
    private $branchName;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", nullable=true)
     */
    private $remark;

    /**
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="5M")
     */
    public $file;

    public $temp;
    
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
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param array $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return float
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * @param float $deposit
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * @return float
     */
    public function getTotalDepositedAmount()
    {
        return $this->totalDepositedAmount;
    }

    /**
     * @param float $totalDepositedAmount
     */
    public function setTotalDepositedAmount($totalDepositedAmount)
    {
        $this->totalDepositedAmount = $totalDepositedAmount;
    }

    /**
     * @return User
     */
    public function getDepositedBy()
    {
        return $this->depositedBy;
    }

    /**
     * @param User $depositedBy
     */
    public function setDepositedBy($depositedBy)
    {
        $this->depositedBy = $depositedBy;
    }

    /**
     * @return \DateTime
     */
    public function getDepositedAt()
    {
        return $this->depositedAt;
    }

    /**
     * @param \DateTime $depositedAt
     */
    public function setDepositedAt($depositedAt)
    {
        $this->depositedAt = $depositedAt;
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
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
    }

    /**
     * @return string
     */
    public function getBranchName()
    {
        return $this->branchName;
    }

    /**
     * @param string $branchName
     */
    public function setBranchName($branchName)
    {
        $this->branchName = $branchName;
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
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/sales/cash-deposit-slip';
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function removeFile($file)
    {
        $file_path = $this->getUploadRootDir().'/'.$file;

        if(file_exists($file_path)) unlink($file_path);
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    public function isPathExist()
    {
        if($this->path != null){
            return false;
        }
        return true;
    }
}