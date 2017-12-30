<?php

namespace Rbs\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * BankBranchRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BankBranchRepository extends EntityRepository
{
    public function getBranchListWithBankName()
    {
        $qb = $this->createQueryBuilder('branch')
            ->select("bank.name AS bankName, branch.name as branchName, branch.id")
            ->join('branch.bank', 'bank')
            ->addOrderBy('bank.name', 'asc')
            ->addOrderBy('branch.name', 'asc')
            ->getQuery()->getResult();

        $data = array();
        foreach ($qb as $row) {
            $data[$row['id']] = $row['bankName'] . ' - ' . $row['branchName'];
        }

        return $data;
    }
}