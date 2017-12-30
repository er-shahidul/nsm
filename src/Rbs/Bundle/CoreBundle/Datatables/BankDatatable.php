<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class BankDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class BankDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('bank_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder->add('name', 'column', array('title' => 'Name',));

        $this->columnBuilder->add(null, 'action', array(
            'width' => '180px',
            'title' => 'Action',
            'actions' => array(
                $this->makeActionButton('bank_update', array('id' => 'id'), 'ROLE_HEAD_OFFICE_USER', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\Bank';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bank_datatable';
    }
}
