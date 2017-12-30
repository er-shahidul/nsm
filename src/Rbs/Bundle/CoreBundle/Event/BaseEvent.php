<?php
namespace Rbs\Bundle\CoreBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Xiidea\EasyAuditBundle\Resolver\EmbeddedEventResolverInterface;

abstract class BaseEvent extends Event implements EmbeddedEventResolverInterface
{
    protected function humanize($value)
    {
        return ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $value))));
    }
}