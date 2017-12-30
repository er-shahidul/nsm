<?php
namespace Rbs\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\CoreBundle\Entity\ItemPriceLog;
use Rbs\Bundle\UserBundle\Entity\User;

class ItemPriceLogRepository extends EntityRepository
{
    public function itemPriceLog(User $user, $id, $currentPrice, $previousPrice)
    {
        $itemPriceLog = new ItemPriceLog();
        $itemPriceLog->setItem($this->_em->getRepository('RbsCoreBundle:Item')->find($id));
        $itemPriceLog->setCurrentPrice($currentPrice);
        $itemPriceLog->setPreviousPrice($previousPrice);
        $itemPriceLog->setCreatedAt(new \DateTime());
        $itemPriceLog->setUpdatedAt(new \DateTime());
        $itemPriceLog->setCreatedBy($user);
        $itemPriceLog->setUpdatedBy($user);

        $this->_em->persist($itemPriceLog);
        $this->_em->flush();
    }
}