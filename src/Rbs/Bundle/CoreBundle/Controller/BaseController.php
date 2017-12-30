<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\Event;

class BaseController extends Controller
{
    protected function dispatch($eventName, Event $event)
    {
        $this->get('event_dispatcher')->dispatch($eventName, $event);
    }

    protected function flashMessage($key, $message)
    {
        $this->get('session')->getFlashBag()->add($key, $message);
    }
}
