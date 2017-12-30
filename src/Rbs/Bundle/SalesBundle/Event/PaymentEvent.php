<?php
namespace Rbs\Bundle\SalesBundle\Event;

use Rbs\Bundle\CoreBundle\Event\BaseEvent;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\Payment;

class OrderApproveEvent extends BaseEvent
{
    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @var Agent
     */
    protected $agent;

    /**
     * @var String
     */
    protected $eventName;

    public function __construct(Payment $payment, Agent $agent)
    {
        $this->payment = $payment;
        $this->agent = $agent;
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
        );
    }

    /**
     * @param string $typeName
     * @return string
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
        if ($this->payment->getAmount() > 0) {
            $descriptionTemplate = "Payment %s has been deposit by #%s to %s"; // amount, agent ID, Bank and Branch Name
            $orderIds = $this->getOrderIds();

            if ($orderIds) {
                $descriptionTemplate .= "for Order %s"; // Order IDS separated by comma
            }

            $bankInfo = $this->payment->getBankName() . ", " . $this->payment->getBranchName();
            $description = sprintf($descriptionTemplate,
                number_format($this->payment->getAmount(), 2),
                $this->agent->getId(),
                $bankInfo,
                $orderIds
            );
        } else {
            $descriptionTemplate = "Payment %s has subtract from #%s for %s"; // amount, agent ID, remarks

            $description = sprintf($descriptionTemplate,
                number_format($this->payment->getAmount(), 2),
                $this->agent->getId(),
                $this->payment->getRemark()
            );
        }

        return $description;
    }

    protected function getOrderIds()
    {
        $ordersIds = array();
        if ($orders = $this->payment->getOrders()) {
            foreach ($this->payment->getOrders() as $order) {
                $ordersIds[] = $order->getId();
            }
        }

        return implode(", ", $ordersIds);
    }
}