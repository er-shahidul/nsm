<?php

namespace Rbs\Bundle\SalesBundle\Datatables;
use Rbs\Bundle\SalesBundle\Entity\Vehicle;

/**
 * Class VehicleDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class VehicleDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var Vehicle $vehicle
         * @return mixed
         */
        $formatter = function($line){

            if(isset($line['deliveries']) && !empty($line['deliveries'])) {
                $delivery = $this->em->getRepository('RbsSalesBundle:Delivery')->find($line['deliveries']['id']);
                $line["orderList"] = $delivery->getOrdersString();

            }
            $vehicle = $this->em->getRepository('RbsSalesBundle:Vehicle')->find($line['id']);
            $line['isDeliveryFalse'] = $vehicle->isDeliveryFalse();
            if(!empty($line['agent'])){
                $profile = $this->em->getRepository('RbsUserBundle:Profile')->findOneBy(array('user' => $line['agent']['user']['id']));
                $line["fullName"] = $profile->getFullName();
            }else{
                $line["fullName"] = 'NOURISH';
            }

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
        $this->options->setOptions(array_merge($this->defaultOptions(), array('order' => [[0, 'desc']])));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('truck_info_list_ajax'),
            'type' => 'GET'
        ));

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->columnBuilder
            ->add('createdAt', 'datetime', array('title' => 'Date', 'date_format' => $dateFormat));
        $this->columnBuilder
            ->add('agent.user.id', 'column', array('title' => 'Agent/Nourish', 'render' => 'resolveAgentName'));
        $this->columnBuilder
            ->add('deliveries.id', 'column', array('visible' => false))
            ->add('orderList', 'virtual', array('title' => 'Orders'))
            ->add('driverName', 'column', array('title' => 'Driver Name'))
            ->add('depo.name', 'column', array('title' => 'Depot Name'))
            ->add('driverPhone', 'column', array('title' => 'Driver Phone'))
            ->add('truckNumber', 'column', array('title' => 'Truck Number'))
            ->add('status', 'column', array('title' => 'Status'))
            ->add('smsText', 'column', array('title' => 'SMS Text'))
            ->add('isDeliveryFalse', 'virtual', array('visible' => false))
            ->add(null, 'action', array(
                'width' => '',
                'title' => 'Action',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
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
                            'data-target' => "#vehicleView",
                            'data-toggle'=>"modal"
                        ),
                        'render_if' => array('isDeliveryFalse')
//                        'role' => 'ROLE_USER',
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
        return 'truck_info_datatable';
    }
}
