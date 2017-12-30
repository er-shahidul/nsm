<?php

namespace Rbs\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DepoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DepoRepository extends EntityRepository
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

    public function getDepoId($user)
    {
        $query = $this->createQueryBuilder('d');
        $query->join('d.users', 'u');
        $query->select('d.id');
        $query->where('u.id = :user');
        $query->setParameter('user', $user);

        return $query->getQuery()->getResult();
    }

    public function getDepoForCurrentUser($user)
    {
        $query = $this->createQueryBuilder('d');
        $query->join('d.users', 'u');
        $query->where('u.id = :user');
        $query->setMaxResults(1);
        $query->setParameter('user', $user->getId());

        return $query->getQuery()->getResult();
    }

    public function getAllActiveDepo()
    {
        $query = $this->createQueryBuilder('d');
        $query->select('d.name');
        $query->where('d.deletedAt IS NULL');
        $query->andWhere('d.usedInTransport = 1');
        $query->orderBy('d.name', 'ASC');

        return $query->getQuery()->getResult();
    }
}
