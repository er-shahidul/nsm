<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

/**
 * Class VehicleDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class MyVehicleDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions(array_merge($this->defaultOptions(), array('order' => [[0, 'desc']])));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('truck_info_my_list_ajax'),
            'type' => 'GET'
        ));

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->columnBuilder
            ->add('createdAt', 'datetime', array('title' => 'Date', 'date_format' => $dateFormat))
            ->add('driverName', 'column', array('title' => 'Driver Name'))
            ->add('driverPhone', 'column', array('title' => 'Driver Phone'))
            ->add('truckNumber', 'column', array('title' => 'Truck Number'))
            ->add('transportStatus', 'column', array('title' => 'Status'))
            ->add('smsText', 'column', array('title' => 'SMS Text'))
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
        return 'my_truck_info_datatable';
    }
}
