<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

/**
 * Class VendorDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class VendorDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('vendor_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('vendorName', 'column', array('title' => 'Name',))
            ->add('contractPerson', 'column', array('title' => 'Contract Person',))
            ->add('contractNo', 'column', array('title' => 'Contract No',))
            ->add('area.areaName', 'column', array('title' => 'Area',))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'actions' => array(
                    $this->makeActionButton('vendor_edit', array('id' => 'id'), 'ROLE_ADMIN', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
                    $this->makeActionButton('vendor_delete', array('id' => 'id'), 'ROLE_ADMIN', 'Delete', 'Delete', 'fa fa-trash-o', 'btn btn-default btn-xs delete-list-btn'),
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\Vendor';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vendor_datatable';
    }
}
