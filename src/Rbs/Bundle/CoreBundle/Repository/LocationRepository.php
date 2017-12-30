<?php
namespace Rbs\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
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

    public function getSectors()
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 1');

        return $query->getQuery()->getResult();
    }

    public function getZones()
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 2');

        return $query->getQuery()->getResult();
    }

    public function getZoneBySector($parentId)
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 2');
        $query->andWhere('l.parentId = :parentId');
        $query->setParameter('parentId', $parentId);

        return $query->getQuery()->getResult();
    }

    public function getRegionBySector($parentId)
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 3');
        $query->andWhere('l.parentId = :parentId');
        $query->setParameter('parentId', $parentId);

        return $query->getQuery()->getResult();
    }

    public function getDistrictByRegion($parentId)
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 4');
        $query->andWhere('l.parentId = :parentId');
        $query->setParameter('parentId', $parentId);

        return $query->getQuery()->getResult();
    }

    public function getUpozillaByDistrict($parentId)
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 5');
        $query->andWhere('l.parentId = :parentId');
        $query->setParameter('parentId', $parentId);

        return $query->getQuery()->getResult();
    }

    public function getZillaByParentId($data)
    {
        $query = $this->createQueryBuilder('i');
        $query->select('i.name');

        foreach ($data['item'] as $item) {
            $query->andWhere('i.id = :item');
            $query->setParameter('item', $item);
        }

        return $query->getQuery()->getResult();
    }

    public function getAllFromSectorToUpozilla()
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 5 or l.level = 4 or l.level = 3 or l.level = 2 or l.level = 1');

        return $query->getQuery()->getResult();
    }

    public function getRegions()
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 3');

        return $query->getQuery()->getResult();
    }

    public function getDistricts()
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 4');

        return $query->getQuery()->getResult();
    }

    public function getUpozillas()
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 5');

        return $query->getQuery()->getResult();
    }

    public function getUnions()
    {
        $query = $this->createQueryBuilder('l');
        $query->where('l.level = 6');

        return $query->getQuery()->getResult();
    }
}