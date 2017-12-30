<?php
namespace Rbs\Bundle\CoreBundle\Event;


use Rbs\Bundle\CoreBundle\Entity\Item;
use Symfony\Component\EventDispatcher\Event;

class ItemEvent extends Event
{
    protected $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function getItem()
    {
        return $this->item;
    }
}