<?php
namespace Rbs\Bundle\SalesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\UserBundle\Entity\User;

class ChickenSetForAgentRepository extends EntityRepository
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

    public function findAgentsUsingParentId($parentId)
    {
        $query = $this->createQueryBuilder('csfa');
        $query->join('csfa.agent', 'a');
        $query->join('a.user', 'u');
        $query->where('u.userType = :AGENT');
        $query->andWhere('u.parentId = :parentId');
        $query->setParameter('AGENT', User::AGENT);
        $query->setParameter('parentId', $parentId);

        return $query->getQuery()->getResult();
    }

    public function findAgentsUsingZilla($zilla)
    {
        $query = $this->createQueryBuilder('csfa');
        $query->join('csfa.agent', 'a');
        $query->join('a.user', 'u');
        $query->where('u.userType = :AGENT');
        $query->andWhere('u.zilla = :zilla');
        $query->setParameter('AGENT', User::AGENT);
        $query->setParameter('zilla', $zilla);

        return $query->getQuery()->getResult();
    }
}