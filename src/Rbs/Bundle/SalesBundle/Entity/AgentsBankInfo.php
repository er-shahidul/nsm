<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * AgentsBankInfo
 *
 * @ORM\Table(name="sales_agents_bank_info")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\AgentsBankInfoRepository")
 * @ORMSubscribedEvents()
 * @ORM\HasLifecycleCallbacks
 */
class AgentsBankInfo
{
    const ACTIVE = 'ACTIVE';
    const INACTIVE = 'INACTIVE';
    const VERIFIED = 'VERIFIED';
    const APPROVED = 'APPROVED';
    const CANCEL = 'CANCEL';

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
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Agent", inversedBy="agentsBankInfo", cascade={"persist"})
     * @ORM\JoinColumn(name="agent_id", nullable=false)
     */
    private $agent;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Order")
     * @ORM\JoinColumn(name="order_id", nullable=true, onDelete="CASCADE")
     */
    private $orderRef;

    /**
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('ACTIVE', 'INACTIVE', 'VERIFIED', 'APPROVED', 'CANCEL')", nullable=false)
     */
    private $status = 'ACTIVE';

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount = 0 ;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="approved_by", nullable=true)
     */
    private $approvedBy;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="verified_by", nullable=true)
     */
    private $verifiedBy;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="cancel_by", nullable=true)
     */
    private $cancelBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="verified_at", type="datetime", nullable=true)
     */
    private $verifiedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cancel_at", type="datetime", nullable=true)
     */
    private $cancelAt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", nullable=true)
     */
    private $remark;

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
     * @return Order
     */
    public function getOrderRef()
    {
        return $this->orderRef;
    }

    /**
     * @param Order $order
     */
    public function setOrderRef($order)
    {
        $this->orderRef = $order;
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
     * @return User
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param User $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return User
     */
    public function getVerifiedBy()
    {
        return $this->verifiedBy;
    }

    /**
     * @param User $verifiedBy
     */
    public function setVerifiedBy($verifiedBy)
    {
        $this->verifiedBy = $verifiedBy;
    }

    /**
     * @return \DateTime
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * @param \DateTime $approvedAt
     */
    public function setApprovedAt($approvedAt)
    {
        $this->approvedAt = $approvedAt;
    }

    /**
     * @return mixed
     */
    public function getVerifiedAt()
    {
        return $this->verifiedAt;
    }

    /**
     * @param mixed $verifiedAt
     */
    public function setVerifiedAt($verifiedAt)
    {
        $this->verifiedAt = $verifiedAt;
    }

    /**
     * @return User
     */
    public function getCancelBy()
    {
        return $this->cancelBy;
    }

    /**
     * @param User $cancelBy
     */
    public function setCancelBy($cancelBy)
    {
        $this->cancelBy = $cancelBy;
    }

    /**
     * @return \DateTime
     */
    public function getCancelAt()
    {
        return $this->cancelAt;
    }

    /**
     * @param \DateTime $cancelAt
     */
    public function setCancelAt($cancelAt)
    {
        $this->cancelAt = $cancelAt;
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
        return 'uploads/sales/agent-bank-slip';
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

    public function isApproved()
    {
        if($this->approvedBy != null and $this->approvedAt != null){
            return true;
        }
        return false;
    }

    public function isVerified()
    {
        if($this->verifiedBy != null and $this->verifiedAt != null){
            return true;
        }
        return false;
    }

    public function isCancel()
    {
        if($this->cancelBy != null and $this->cancelAt != null){
            return true;
        }
        return false;
    }
}