<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

/**
 * Class ItemDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class ItemDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        $formatter = function($line){
            $line['enabled'] = $line['status'] === 1;
            $line['disabled'] = $line['status'] === 0;

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
            'url' => $this->router->generate('item_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('name', 'column', array('title' => 'Name'))
            ->add('sku', 'column', array('title' => 'Item Code'))
            ->add('itemUnit', 'column', array('title' => 'Item Unit'))
            ->add('itemType.itemType', 'column', array('title' => 'Item Type'))
            ->add('category.name', 'array', array('title' => 'Category', 'data' => 'category[, ].name'))
            ->add('status', 'boolean', array(
                'title' => 'Status',
                'true_icon' => 'fa fa-check-circle-o',
                'false_icon' => 'fa fa-ban',
                'true_label' => 'Enabled',
                'false_label' => 'Disabled'
                )
            )
            ->add('bundles.name', 'array', array('title' => 'Modules', 'data' => 'bundles[, ].name'))
            ->add(null, 'action', array(
                'width' => '330px',
                'title' => 'Action',
                'actions' => array(
                    $this->makeActionButton('item_edit', array('id' => 'id'), 'ROLE_ADMIN', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
//                    $this->makeActionButton('item_delete', array('id' => 'id'), 'ROLE_ADMIN', 'Delete', 'Delete', 'fa fa-trash-o', 'btn btn-default btn-xs delete-list-btn'),
                    array(
                        'route' => 'set_item_price',
                        'route_parameters' => array('id' => 'id'),
                        'label' => 'Set Price',
                        'icon' => 'fa fa-pencil-square-o',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Set Price',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                            'data-target' => "#price-set-modal",
                            'data-toggle' => "modal"
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_ADMIN',
                    ),
                    $this->makeActionButton('ItemPriceLogs_home', array('id' => 'id'), 'ROLE_ADMIN', 'Price Log', 'Price Log', 'fa fa-pencil-square-o'),
                    $this->makeActionButton('item_statue_change', array('id' => 'id'), 'ROLE_ADMIN', 'Enable', 'Enable', 'fa fa-check-circle-o', 'btn btn-default btn-xs confirmation-btn delete-list-btn', array('render_if' => arraY('disabled'))),
                    $this->makeActionButton('item_statue_change', array('id' => 'id'), 'ROLE_ADMIN', 'Disable', 'Disable', 'fa fa-ban', 'btn btn-default btn-xs confirmation-btn delete-list-btn', array('render_if' => array('enabled'))),
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\Item';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'item_datatable';
    }
}
