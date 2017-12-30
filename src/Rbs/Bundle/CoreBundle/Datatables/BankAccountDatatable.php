<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class BankAccountDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class BankAccountDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('bank_account_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
                ->add('name', 'column', array('title' => 'Name',))
                ->add('code', 'column', array('title' => 'Code',))
                ->add('branch.bank.name', 'column', array('title' => 'Bank Name',))
                ->add('branch.name', 'column', array('title' => 'Branch Name',))
                ;

        $this->columnBuilder->add(null, 'action', array(
            'width' => '180px',
            'title' => 'Action',
            'actions' => array(
                $this->makeActionButton('bank_account_update', array('id' => 'id'), 'ROLE_HEAD_OFFICE_USER', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\BankAccount';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bankaccount_datatable';
    }
}
