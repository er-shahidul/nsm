<?php
namespace Rbs\Bundle\CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function getAllActiveCategory()
    {
        $query = $this->createQueryBuilder('c');
        $query->where('c.status = 1');
        $query->andWhere('c.deletedAt IS NULL');

        return $query->getQuery()->getResult();
    }
}