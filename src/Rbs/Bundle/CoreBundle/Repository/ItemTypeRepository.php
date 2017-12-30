<?php
namespace Rbs\Bundle\CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ItemTypeRepository extends EntityRepository
{
    public function getAllActiveItemType()
    {
        $query = $this->createQueryBuilder('it');
        $query->select('it.itemType');
        $query->where('it.deletedAt IS NULL');
        $query->orderBy('it.itemType', 'ASC');

        return $query->getQuery()->getResult();
    }
    public function getItemTypeCount()
    {
        $query = $this->createQueryBuilder('it');
        $query->select('COUNT(it.itemType) as itemTypeCount');
        $query->where('it.deletedAt IS NULL');

        return $query->getQuery()->getSingleScalarResult();
    }
}