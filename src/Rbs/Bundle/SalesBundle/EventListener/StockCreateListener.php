<?php
namespace Rbs\Bundle\SalesBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Rbs\Bundle\CoreBundle\Event\ItemEvent;
use Rbs\Bundle\CoreBundle\Event\DepoEvent;
use Rbs\Bundle\SalesBundle\Entity\Stock;

class StockCreateListener
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function onItemCreated(ItemEvent $itemEvent)
    {
        foreach ($this->em->getRepository('RbsCoreBundle:Depo')->findAll() as $depo) {
            $item = $itemEvent->getItem();

            $stock = new Stock();
            $stock->setItem($item);
            $stock->setDepo($depo);
            $this->em->persist($stock);
            $this->em->flush($stock);
        }
    }

    public function onDepoCreated(DepoEvent $depoEvent)
    {
        foreach ($this->em->getRepository('RbsCoreBundle:Item')->findAll() as $item) {
            $depo = $depoEvent->getDepo();

            $stock = new Stock();
            $stock->setItem($item);
            $stock->setDepo($depo);
            $this->em->persist($stock);
            $this->em->flush($stock);
        }
    }
}