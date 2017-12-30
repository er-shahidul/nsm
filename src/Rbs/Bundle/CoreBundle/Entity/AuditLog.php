<?php
namespace Rbs\Bundle\CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Xiidea\EasyAuditBundle\Entity\BaseAuditLog;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rbs\Bundle\CoreBundle\Repository\AuditLogRepository")
 * @ORM\Table(name="core_audit_log", indexes={
 *   @ORM\Index(name="object_idx", columns={"object_id"}),
 *   @ORM\Index(name="type_idx", columns={"type_id"})
 * })
 */
class AuditLog extends BaseAuditLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Type Of Event(Internal Type ID)
     *
     * @var string
     * @ORM\Column(name="type_id", type="string", length=200, nullable=false)
     */
    protected $typeId;

    /**
     * Type Of Event(Internal Type ID)
     *
     * @var string
     * @ORM\Column(name="object_id", type="integer", nullable=true)
     */
    protected $objectId;

    /**
     * Type Of Event
     *
     * @var string
     * @ORM\Column(name="type", type="string", length=200, nullable=true)
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="reason", type="string", length=255, nullable=true)
     */
    protected $reason;

    /**
     * Time Of Event
     * @var \DateTime
     * @ORM\Column(name="event_time", type="datetime")
     */
    protected $eventTime;

    /**
     * @var string
     * @ORM\Column(name="user", type="string", length=255)
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(name="ip", type="string", length=20, nullable=true)
     */
    protected $ip;

    /**
     * @return string
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param string $objectId
     *
     * @return AuditLog
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     *
     * @return AuditLog
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

}