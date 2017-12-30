<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\Stock;

/**
 * Class StockDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class StockDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var Stock $stock
         * @return mixed
         */
        $formatter = function($line){
            $stock = $this->em->getRepository('RbsSalesBundle:Stock')->find($line['id']);
            $line['available'] = $stock->isAvailableOnDemand();
            $line['notAvailable'] = !$stock->isAvailableOnDemand();
            $line["itemInfo"] = $stock->getItem()->getItemInfo();

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
            'url' => $this->router->generate('stocks_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('itemInfo', 'virtual', array('visible' => false))
            ->add('itemInfo', 'column', array('title' => 'Item name'))
            ->add('onHand', 'column', array('title' => 'On Hand'))
            ->add('onHold', 'column', array('title' => 'On Hold'))
            ->add('depo.name', 'column', array('title' => 'Depo'))
            ->add('available', 'virtual', array('visible' => false))
            ->add('notAvailable', 'virtual', array('visible' => false))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'Action',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'stock_create',
                        'route_parameters' => array(
                            'id' => 'id',
                            'depoId' => 'depo.id'
                        ),
                        'label' => 'Add Stock',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'edit-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                            'data-target' => "#ajax",
                            'data-toggle'=>"modal"
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_STOCK_CREATE',
                    ),
                    array(
                        'route' => 'stock_history',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'History',
                        'icon' => 'glyphicon',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'show-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                            'data-target' => "#ajaxHistory",
                            'data-toggle'=>"modal"
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_STOCK_VIEW',
                    )
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\Stock';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'stock_datatable';
    }
}
