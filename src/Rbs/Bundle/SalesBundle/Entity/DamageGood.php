<?php
namespace Rbs\Bundle\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * DamageGood
 *
 * @ORM\Table(name="sales_damage_goods")
 * @ORM\Entity(repositoryClass="Rbs\Bundle\SalesBundle\Repository\DamageGoodRepository")
 * @ORMSubscribedEvents()
 * @ORM\HasLifecycleCallbacks
 */
class DamageGood
{
    const ACTIVE = 'ACTIVE';
    const INACTIVE = 'INACTIVE';
    const APPROVED = 'APPROVED';
    const VERIFIED = 'VERIFIED';
    const REJECTED = 'REJECTED';
    const PAID = 'PAID';

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
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Agent")
     * @ORM\JoinColumn(name="agent_id", nullable=false)
     */
    private $agent;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    private $user;

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
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Rbs\Bundle\SalesBundle\Entity\Order")
     * @ORM\JoinColumn(name="order_id", nullable=true, onDelete="CASCADE")
     */
    private $orderRef;

    /**
     * @var array $type
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="ENUM('ACTIVE', 'INACTIVE', 'APPROVED', 'VERIFIED', 'PAID', 'REJECTED')", nullable=false)
     */
    private $status = 'ACTIVE';

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Amount should not be zero",
     * )
     */
    private $amount = 0 ;

    /**
     * @var float
     *
     * @ORM\Column(name="refund_amount", type="float")
     */
    private $refundAmount = 0 ;
    
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
     * @var string
     *
     * @ORM\Column(name="reject_reason", type="text", nullable=true)
     */
    private $rejectReason;


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
     * @param string $status
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
     * @return \DateTime
     */
    public function getVerifiedAt()
    {
        return $this->verifiedAt;
    }

    /**
     * @param \DateTime $verifiedAt
     */
    public function setVerifiedAt($verifiedAt)
    {
        $this->verifiedAt = $verifiedAt;
    }

    /**
     * @return mixed
     */
    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    /**
     * @param mixed $refundAmount
     */
    public function setRefundAmount($refundAmount)
    {
        $this->refundAmount = $refundAmount;
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
        return 'uploads/sales/damage-goods';
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

    /**
     * @return string
     */
    public function getRejectReason()
    {
        return $this->rejectReason;
    }

    /**
     * @param string $rejectReason
     *
     * @return DamageGood
     */
    public function setRejectReason($rejectReason)
    {
        $this->rejectReason = $rejectReason;

        return $this;
    }

    public function getDownloadFilePath()
    {
        return empty($this->path) ? '#' : '/'.$this->getUploadDir() . '/' . $this->path;
    }
}