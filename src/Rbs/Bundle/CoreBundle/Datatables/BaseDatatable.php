<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class CategoryDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class BaseDatatable extends AbstractDatatableView
{
    public function defaultFeatures()
    {
        return array(
            'auto_width' => true,
            'defer_render' => false,
            'info' => true,
            'jquery_ui' => false,
            'length_change' => true,
            'ordering' => true,
            'paging' => true,
            'processing' => true,
            'scroll_x' => false,
            'scroll_y' => '',
            'searching' => true,
            'server_side' => true,
            'state_save' => true,
            'delay' => 0
        );
    }

    public function defaultOptions()
    {
        return array(
            'display_start' => 0,
            'defer_loading' => -1,
            'dom' => 'lfrtip',
            'length_menu' => array(10, 25, 50, 100),
            'order_classes' => true,
            'order' => [[0, 'asc']],
            'order_multi' => true,
            'page_length' => 10,
            'paging_type' => Style::FULL_NUMBERS_PAGINATION,
            'renderer' => '',
            'scroll_collapse' => false,
            'search_delay' => 0,
            'state_duration' => 7200,
            'stripe_classes' => array(),
            'responsive' => true,
            'class' => Style::BOOTSTRAP_3_STYLE,
            'individual_filtering' => false,
            'individual_filtering_position' => 'foot',
            'use_integration_options' => false
        );
    }

    public function getEntity()
    {
    }

    public function getName()
    {
    }

    protected function makeActionButton($route, $routeParam = array(), $role = 'ROLE_USER', $label = "", $buttonTitle = "", $icon = "", $btnClass = "btn btn-primary btn-xs", $additional = array())
    {
        $data = array(
            'route' => $route,
            'route_parameters' => $routeParam,
            'label' => $label,
            'icon' => $icon,
            'attributes' => array(
                'rel' => 'tooltip',
                'title' => $buttonTitle,
                'class' => $btnClass,
                'role' => 'button'
            ),
            'confirm' => false,
            'confirm_message' => 'Are you sure?',
            'role' => $role,
        );
        return array_merge($data, $additional);
    }
}
