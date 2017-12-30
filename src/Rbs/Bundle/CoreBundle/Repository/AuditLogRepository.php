<?php
namespace Rbs\Bundle\CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\CoreBundle\Entity\AuditLog;

class AuditLogRepository extends EntityRepository
{
    public function getByTypeOrObjectId($types = array(), $objectId = null)
    {
        $qb = $this->createQueryBuilder('a');
        if (!empty($types)) {
            $qb->where('a.typeId IN (:types)')->setParameter('types', $types);
        }
        if ($objectId) {
            $qb->andWhere('a.objectId = :objectId')->setParameter('objectId', $objectId);
        }
        $qb->orderBy('a.eventTime', 'ASC');

        /*$data = array();
        foreach ($types as $type) {
            $data[$type] = array();
        }*/

        /** @var AuditLog $auditLog */
        /*foreach ($qb->getQuery()->getResult() as $auditLog) {
            $data[$auditLog->getTypeId()][] = $auditLog;
        }*/

        return $qb->getQuery()->getResult();
    }
}