<?php
namespace Rbs\Bundle\CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\SalesBundle\RbsSalesBundle;

class ItemRepository extends EntityRepository
{
    public function allItems()
    {
        $query = $this->createQueryBuilder('i');
        $query->join('i.bundles', 'b');
        $query->select('i.id');
        $query->addSelect('i.name');
        $query->where('b.id = :bundle');
        $query->setParameter('bundle', RbsSalesBundle::ID);
        $query->groupBy('i.id');
        $query->orderBy('i.name');

        $result = $query->getQuery()->getResult();

        $data = array();
        foreach($result as $row) {
            $data[$row['id']] = $row;
        }

        return $data;
    }
    
    public function getItemName()
    {
        $query = $this->createQueryBuilder('l');
        $query->select('l.name');
        $query->where('l.level = 5');

        foreach ($data['zilla'] as $zilla){
            $query->andWhere('l.parentId = :zilla');
            $query->setParameter('zilla', $zilla);
        }

        return $query->getQuery()->getResult();
    }


}