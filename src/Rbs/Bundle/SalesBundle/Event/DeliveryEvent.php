<?php
namespace Rbs\Bundle\SalesBundle\Event;

use Rbs\Bundle\CoreBundle\Event\BaseEvent;
use Rbs\Bundle\SalesBundle\Entity\Delivery;
use Rbs\Bundle\SalesBundle\Entity\Order;

class DeliveryEvent extends BaseEvent
{
    /**
     * @var Delivery
     */
    protected $delivery;

    /** @var Order */
    protected $order;

    /**
     * @var String
     */
    protected $eventName;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
        $this->order = $delivery->getOrders();
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
//        $eventDescription = $this->getDescriptionString();

        return array(
//            'description' => $eventDescription,
            'type' => $eventType,
        );
    }

    /**
     * @return string
     * @internal param string $typeName
     */
    protected function getEventShortName()
    {
        return substr(strrchr($this->eventName, '.'), 1);
    }

    protected function getEventType()
    {
        return ucwords(str_replace('.', ' ', $this->eventName));
    }

    protected function getDescriptionString()
    {
        $descriptionTemplate = "Order #%s %s";

        switch ($this->getEventShortName()) {
            case 'vehicle_in': $descriptionTemplate = "Order #%s Vehicle In"; break;
            case 'vehicle_out': $descriptionTemplate = "Order #%s Vehicle Out"; break;
            case 'start_loading': $descriptionTemplate = "Order #%s Start Loading"; break;
            case 'finish_loading': $descriptionTemplate = "Order #%s Finish Loading"; break;
        }

        $description = sprintf($descriptionTemplate,
            $this->order->getId(),
            $this->order->getDeliveryState()
        );

        return $description;
    }
}