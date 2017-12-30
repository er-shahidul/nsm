<?php
namespace Rbs\Bundle\CoreBundle\Event;

use Rbs\Bundle\CoreBundle\Entity\Depo;
use Symfony\Component\EventDispatcher\Event;

class DepoEvent extends Event
{
    protected $depo;

    public function __construct(Depo $depo)
    {
        $this->depo = $depo;
    }

    public function getDepo()
    {
        return $this->depo;
    }
}