<?php

namespace Rbs\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\CoreBundle\Entity\TransportIncentive;

/**
 * TransportIncentiveRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransportIncentiveRepository extends EntityRepository
{
    public function getAll()
    {
        return $this->findAll();
    }

    public function create($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
    }

    public function delete($data)
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }

    public function update($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
        return $this->_em;
    }

    public function getAllTransportIncentive()
    {
        $query = $this->createQueryBuilder('ti');
        $query->join('ti.itemType', 'it');
        $query->join('ti.depo', 'd');
        $query->join('ti.district', 'dis');
        $query->join('ti.station', 'sta');
        $query->select('ti.amount');
        $query->addSelect('it.itemType');
        $query->addSelect('d.name as depo');
        $query->addSelect('dis.name as district');
        $query->addSelect('sta.name as station');
        $query->where('ti.deletedAt IS NULL');
        $query->andWhere('d.usedInTransport = 1');
        $query->andWhere('ti.status = :CURRENT');
        $query->orderBy('dis.name', 'ASC');
        $query->setParameters(array('CURRENT'=>TransportIncentive::CURRENT));

        return $query->getQuery()->getResult();
    }

    public function getTransportIncentive($station, $dipo, $itemType)
    {
        $query = $this->createQueryBuilder('ti');
        $query->join('ti.itemType', 'it');
        $query->join('ti.district', 'dis');
        $query->join('ti.station', 'sta');
        $query->select('ti.amount');
        $query->where('ti.deletedAt IS NULL');
        $query->andWhere('ti.station = :station');
        $query->andWhere('ti.depo = :dipo');
        $query->andWhere('ti.itemType = :itemType');
        $query->andWhere('ti.status = :CURRENT');
        $query->setParameters(array('station'=>$station, 'dipo'=>$dipo, 
            'itemType'=>$itemType, 'CURRENT'=>TransportIncentive::CURRENT));

        return $query->getQuery()->getResult();
    }
    
    public function getTransportIncentiveForStatusChange($district, $station, $dipo, $itemType)
    {
        $query = $this->createQueryBuilder('ti');
        $query->where('ti.deletedAt IS NULL');
        $query->andWhere('ti.station = :station');
        $query->andWhere('ti.district = :district');
        $query->andWhere('ti.depo = :dipo');
        $query->andWhere('ti.itemType = :itemType');
        $query->andWhere('ti.status = :CURRENT');
        $query->setParameters(array('district'=>$district, 'station'=>$station, 'dipo'=>$dipo,
            'itemType'=>$itemType, 'CURRENT'=>TransportIncentive::CURRENT));

        return $query->getQuery()->getResult();
    }
}
