<?php

namespace Rbs\Bundle\SalesBundle\Helper;

use Doctrine\ORM\EntityManager;
use Rbs\Bundle\SalesBundle\Entity\Delivery;
use Rbs\Bundle\SalesBundle\Entity\Vehicle;
use Rbs\Bundle\UserBundle\Entity\User;

class SmsVehicleParse
{
    /** @var  EntityManager */
    protected $em;

    public $error;

    /** @var User */
    protected $user;

    /** @var Vehicle */
    protected $vehicle ;
    
    /** @var Delivery */
    protected $delivery ;

    public function __construct($em, $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

    protected function setError($string)
    {
        $this->error = $string;
    }

    protected function hasError()
    {
        return !empty($this->error);
    }

    public function parse($message)
    {
        $this->error;
        $this->validate($message);
        return $this->create($message);
    }

    protected function validate($message)
    {
        $splitMsg = array_filter(explode(';', $message));
        $splitMsgVehicles = array_filter(explode(':', $splitMsg[0]));

        if (sizeof($splitMsg)>1){
            if($this->user->getUserType() == User::AGENT) {
                $order = $this->em->getRepository('RbsSalesBundle:Order')->find($splitMsg[1]);
                if($order == null){
                    $this->setError('Invalid Order Number');
                    return;
                }
            }else{
                $depo =  $this->em->getRepository('RbsCoreBundle:Depo')->findByName($splitMsg[1]);
                if($depo == null){
                    $this->setError('Invalid Depo Name');
                    return;
                }
            }
            foreach($splitMsgVehicles as $vehicle){
                $vehicleInfo = array_filter(explode(',', $vehicle));
                
                if(sizeof($vehicleInfo)>0){
                    if($vehicleInfo[0] == null){
                        $this->setError('Invalid Vehicle Number');
                        return;
                    }
                }
                if(sizeof($vehicleInfo)>1) {
                    if ($vehicleInfo[1] == null) {
                        $this->setError('Invalid Driver Name');
                        return;
                    }
                }
                if(sizeof($vehicleInfo)>2) {
                    if ($vehicleInfo[2] == null) {
                        $this->setError('Invalid Driver Phone Number');
                        return;
                    }
                }
            }
        }else{
            $this->setError('Invalid Parameter');
            return;
        }
    }

    public function create($message)
    {
        if ($this->hasError()) {
            return false;
        }

        $splitMsg = array_filter(explode(';', $message));
        $splitMsgVehicles = array_filter(explode(':', $splitMsg[0]));

        if($this->user->getUserType() == User::AGENT) {
            $order = $this->em->getRepository('RbsSalesBundle:Order')->find($splitMsg[1]);
            $this->delivery = new Delivery();
            $this->delivery->addOrder($order);
            $this->delivery->setShipped(false);
            $this->delivery->setDepo($order->getDepo());
            $this->delivery->setTransportGiven(Delivery::AGENT);
            $this->em->persist($this->delivery);
            $this->em->flush();
            foreach ($splitMsgVehicles as $splitMsgVehicle) {
                $vehicleInfo = array_filter(explode(',', $splitMsgVehicle));
                $this->vehicle = new Vehicle();
                $this->vehicle->setTruckNumber($vehicleInfo[0]);
                $this->vehicle->setDriverName($vehicleInfo[1]);
                $this->vehicle->setDriverPhone($vehicleInfo[2]);
                $this->vehicle->setSmsText($message);
                $this->vehicle->setShipped(false);
                $this->vehicle->setAgent($order->getAgent());
                $this->vehicle->setTransportGiven(Vehicle::AGENT);
                $this->vehicle->setDepo($order->getDepo());
                $this->vehicle->setOrderText($order->getId());
                $this->vehicle->setDeliveries($this->delivery);
                $this->em->persist($this->vehicle);
                $this->em->flush();
            }
        }else{
            foreach ($splitMsgVehicles as $splitMsgVehicle) {
                $vehicleInfo = array_filter(explode(',', $splitMsgVehicle));
                $depo =  $this->em->getRepository('RbsCoreBundle:Depo')->findByName($splitMsg[1]);
                $this->vehicle = new Vehicle();
                $this->vehicle->setTruckNumber($vehicleInfo[0]);
                $this->vehicle->setDriverName($vehicleInfo[1]);
                $this->vehicle->setDriverPhone($vehicleInfo[2]);
                $this->vehicle->setSmsText($message);
                $this->vehicle->setShipped(false);
                $this->vehicle->setDepo($depo[0]);
                $this->vehicle->setTransportGiven(Vehicle::NOURISH);
                $this->em->persist($this->vehicle);
                $this->em->flush();
            }
        }

        return array(
            'vehicleId' => $this->vehicle->getId()
        );
    }
}