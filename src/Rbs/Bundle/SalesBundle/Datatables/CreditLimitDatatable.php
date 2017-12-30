<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\CreditLimit;
use Rbs\Bundle\UserBundle\Entity\User;

/**
 * Class CreditLimitDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class CreditLimitDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var CreditLimit $creditLimit
         * @return mixed
         */
        $formatter = function($line){
            $creditLimit = $this->em->getRepository('RbsSalesBundle:CreditLimit')->find($line['id']);
            $line['fullName'] = Agent::agentIdNameFormat($creditLimit->getAgent()->getAgentID(), $creditLimit->getAgent()->getUser()->getProfile()->getFullName());

            return $line;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('credit_limit_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('id', 'column', array('visible' => false))
            ->add('createdAt', 'datetime', array('title' => 'Created Date', 'date_format' => 'LLL' ))
            ->add('fullName', 'virtual', array('title' => 'Agent'))
            ->add('category.name', 'column', array('title' => 'Category'))
            ->add('startDate', 'datetime', array('title' => 'Start Date', 'date_format' => 'LL' ))
            ->add('endDate', 'datetime', array('title' => 'End Date', 'date_format' => 'LL' ))
            ->add('status', 'column', array('title' => 'Status'))
            ->add('amount', 'column', array('title' => 'amount'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\CreditLimit';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'credit_limit_datatable';
    }
}
