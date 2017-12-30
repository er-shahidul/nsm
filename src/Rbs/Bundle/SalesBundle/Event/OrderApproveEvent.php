<?php
namespace Rbs\Bundle\SalesBundle\Event;

use Rbs\Bundle\CoreBundle\Event\BaseEvent;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Symfony\Component\HttpFoundation\Request;

class OrderApproveEvent extends BaseEvent
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var String
     */
    protected $eventName;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Order $order, Request $request)
    {
        $this->order = $order;
        $this->request = $request;
    }

    /**
     * @param $eventName
     *
     * @return array
     */
    public function getEventLogInfo($eventName)
    {
        $this->eventName = $eventName;

        $eventType = $this->getEventType();
        $eventDescription = $this->getDescriptionString();

        return array(
            'description' => $eventDescription,
            'type' => $eventType,
            'objectId' => $this->order->getId(),
            'reason' => $this->request->request->get('reason')
        );
    }

    /**
     * @param string $typeName
     * @return string
     */
    protected function getEventShortName()
    {
        return substr($this->eventName, strpos($this->eventName, '.') + 1);
    }

    protected function getEventType()
    {
        return ucwords(str_replace('.', ' ', $this->eventName));
    }

    protected function getDescriptionString()
    {
        $descriptionTemplate = 'AAA';
        if (strpos($this->eventName, 'order') === 0) {
            $descriptionTemplate = "Order %s for Order #%s";
        } else if(strpos($this->eventName, 'payment') === 0) {
            $descriptionTemplate = "Payment %s for Order #%s";
        } else if(strpos($this->eventName, 'payment') === 0) {
            $descriptionTemplate = "Order %s for Order #%s";
        }

        return sprintf($descriptionTemplate,
            ucfirst($this->getEventShortName()),
            $this->order->getId()
        );
    }
}