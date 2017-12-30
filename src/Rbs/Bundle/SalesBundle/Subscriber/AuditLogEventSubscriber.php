<?php

namespace Rbs\Bundle\SalesBundle\Subscriber;

use Xiidea\EasyAuditBundle\Subscriber\EasyAuditEventSubscriberInterface;

class AuditLogEventSubscriber implements EasyAuditEventSubscriberInterface
{
    public function getSubscribedEvents()
    {
        return array(
            "order.approved",
            "order.canceled",
            "order.hold",
            "order.completed",
            "order.verified",
            "payment.approved",
            "payment.over.credit.approved",
            "delivery.hold",
            "delivery.shipped.partially",
            "delivery.shipped",
            "payment.add",
            "delivery.delivered",
            "delivery.vehicle_in",
            "delivery.vehicle_out",
            "delivery.start_loading",
            "delivery.finish_loading",
        );
    }
}