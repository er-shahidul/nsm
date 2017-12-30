<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class BankBranchDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class BankBranchDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('bank_branch_list_ajax'),
            'type' => 'GET'
        ));
        $this->columnBuilder
            ->add('bank.name', 'column', array('title' => 'Bank Name',))
            ->add('name', 'column', array('title' => 'Branch Name',))
        ;

        $this->columnBuilder->add(null, 'action', array(
            'width' => '180px',
            'title' => 'Action',
            'actions' => array(
                $this->makeActionButton('bankbranch_update', array('id' => 'id'), 'ROLE_HEAD_OFFICE_USER', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\BankBranch';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bankbranch_datatable';
    }
}
