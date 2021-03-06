<?php

namespace Rbs\Bundle\SalesBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AgentsBankInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AgentDocRepository extends EntityRepository
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

    public function getAllFileForLogInAgent($agentId)
    {
        $query = $this->createQueryBuilder('ad');
        $query->join('ad.agent', 'a');
        $query->join('a.user', 'u');
        $query->where('a.id = :agentId');
        $query->setParameter('agentId', $agentId);

        return $query->getQuery()->getResult();
    }
}
