<?php

namespace Rbs\Bundle\SalesBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;


class SmsSender
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var SmsGateWay
     */
    private $smsGateWay;
    
    public function __construct($doctrine, SmsGateWay $smsGateWay)
    {
        $this->doctrine   = $doctrine;
        $this->smsGateWay = $smsGateWay;
    }

    public function agentBankInfoSmsAction($msg, $cellPhone)
    {
        $this->smsGateWay->send($msg, $cellPhone);
    }
}