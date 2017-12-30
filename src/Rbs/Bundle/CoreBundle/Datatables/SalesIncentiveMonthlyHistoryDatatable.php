<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

use Rbs\Bundle\CoreBundle\Entity\SaleIncentive;

/**
 * Class SalesIncentiveMonthlyHistoryDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class SalesIncentiveMonthlyHistoryDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        $formatter = function($line){
            $line['quantity2'] = ($line['quantity'] / 1000) . SaleIncentive::LABEL_TON;
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
            'url' => $this->router->generate('sale_incentive_monthly_history_list_ajax'),
            'type' => 'GET'
        ));
        
        $this->columnBuilder
            ->add('updatedAt', 'datetime', array('title' => 'Date', 'date_format' => 'LLL' ))
            ->add('updatedBy.username', 'column', array('title' => 'Created By'))
            ->add('category.name', 'column', array('title' => 'Category'))
            ->add('quantity', 'column', array('visible' => false))
            ->add('quantity2', 'virtual', array('title' => 'Quantity'))
            ->add('amount', 'column', array('title' => 'Amount'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\SaleIncentive';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sale_incentive_monthly_history_datatable';
    }
}
