<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\Sms;

/**
 * Class SmsDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class SmsDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions(array_merge($this->defaultOptions(), array('order' => [[0, 'desc']])));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('sms_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('date', 'datetime', array(
                    'title' => 'Date',
                    'date_format' => 'LLL' ))
            ->add('mobileNo', 'column', array('title' => 'Mobile'))
            ->add('msg', 'column', array('title' => 'SMS Text'))
            ->add('remark', 'column', array('title' => 'Remark'))
            ->add(null, 'action', array(
                'width' => '120px',
                'title' => 'Action',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'order_create',
                        'route_parameters' => array(
                            'sms' => 'id'
                        ),
                        'label' => 'Add Order',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'add-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_ADMIN',
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
        return 'Rbs\Bundle\SalesBundle\Entity\Sms';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sms_datatable';
    }
}
