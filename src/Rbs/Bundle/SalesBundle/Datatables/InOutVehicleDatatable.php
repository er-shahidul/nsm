<?php

namespace Rbs\Bundle\SalesBundle\Datatables;
use Rbs\Bundle\SalesBundle\Entity\Vehicle;

/**
 * Class InOutVehicleDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class InOutVehicleDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var Vehicle $vehicle
         * @return mixed
         */
        $formatter = function($line){
            $vehicle = $this->em->getRepository('RbsSalesBundle:Vehicle')->find($line['id']);
            $line['isIn'] = $vehicle->isIn();
            $line['isInFalse'] = !$vehicle->isIn();
            $line['isOut'] = $vehicle->isOut();
            $line['isOutFalse'] = !$vehicle->isOut();
            $line['isStart'] = $vehicle->isStart();
            $line['isStartFalse'] = !$vehicle->isStart();
            $line['isFinish'] = $vehicle->isFinish();
            $line['isFinishFalse'] = !$vehicle->isFinish();
            $line['isDeliveryTrue'] = !$vehicle->isDeliveryTrue();
            $line['isDeliveryFalse'] = !$vehicle->isDeliveryFalse();
            $line['isShipped'] = !$vehicle->getShipped();

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
            'url' => $this->router->generate('truck_info_in_out_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('driverName', 'column', array('title' => 'Driver Name'))
            ->add('driverPhone', 'column', array('title' => 'Driver Phone'))
            ->add('truckNumber', 'column', array('title' => 'Truck Number'))
            ->add('transportGiven', 'column', array('title' => 'Given By'))
            ->add('orderText', 'column', array('title' => 'Order Number'))
            ->add('vehicleIn', 'datetime', array('title' => 'In Time', 'date_format' => 'LLL' ))
            ->add('finishLoad', 'datetime', array('title' => 'Finish Load', 'date_format' => 'LLL' ))
            ->add('isIn', 'virtual', array('visible' => false))
            ->add('isOut', 'virtual', array('visible' => false))
            ->add('isStart', 'virtual', array('visible' => false))
            ->add('isFinish', 'virtual', array('visible' => false))
            ->add('isInFalse', 'virtual', array('visible' => false))
            ->add('isOutFalse', 'virtual', array('visible' => false))
            ->add('isStartFalse', 'virtual', array('visible' => false))
            ->add('isFinishFalse', 'virtual', array('visible' => false))
            ->add('isDeliveryTrue', 'virtual', array('visible' => false))
            ->add('isDeliveryFalse', 'virtual', array('visible' => false))
            ->add('isShipped', 'virtual', array('visible' => false))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'truck_in',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Vehicle In',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'enable-action',
                            'class' => 'btn btn-primary btn-xs delete-list-btn green',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_TRUCK_IN',
                        'render_if' => array('isIn')
                    ),
                    array(
                        'route' => 'truck_out',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Vehicle Out',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'disable-action',
                            'class' => 'btn btn-primary btn-xs delete-list-btn red',
                            'role' => 'button'
                        ),
                        'confirm' => false,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_TRUCK_OUT',
                        'render_if' => array('isOut', 'isFinishFalse', 'isShipped')
                    ),
                    array(
                        'route' => 'vehicle_view',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'View',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'edit-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                            'data-target' => "#deliveryView",
                            'data-toggle'=>"modal"
                        ),
                        'role' => 'ROLE_USER',
                        'render_if' => array('isOut', 'isFinishFalse', 'isShipped')
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
        return 'Rbs\Bundle\SalesBundle\Entity\Vehicle';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vehicle_in_out_datatable';
    }
}
