<?php

namespace Rbs\Bundle\SalesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\UserBundle\Entity\User;

/**
 * AgentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AgentRepository extends EntityRepository
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

    public function agents()
    {
        $query = $this->createQueryBuilder('c');
        $query->join('c.user', 'u');
        $query->where('u.userType = :AGENT');
        $query->setParameter('AGENT', User::AGENT);

        return $query->getQuery()->getResult();
    }

    public function getAgentListKeyValue()
    {
        $query = $this->createQueryBuilder('a');
        $query->select('a.id, a.agentID, p.fullName');
        $query->join('a.user', 'u');
        $query->join('u.profile', 'p');
        $query->where('u.userType = :AGENT');
        $query->andWhere('u.enabled = 1');
        $query->andWhere('u.deletedAt IS NULL');

        $query->setParameter('AGENT', User::AGENT);
        $query->orderBy('p.fullName','ASC');

        $result = $query->getQuery()->getResult();

        $output = array();

        $output[''] = 'Select';
        foreach ($result as $agent) {
            $output[$agent['id']] = Agent::agentIdNameFormat($agent['agentID'], $agent['fullName']);
        }

        return $output;
    }

}
