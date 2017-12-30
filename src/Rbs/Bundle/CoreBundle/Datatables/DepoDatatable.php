<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

/**
 * Class DepoDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class DepoDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('depo_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('name', 'column', array('title' => 'Name',))
            ->add('description', 'column', array('title' => 'Description',))
            ->add('location.name', 'column', array('title' => 'Area',))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'actions' => array(
                    $this->makeActionButton('depo_edit', array('id' => 'id'), 'ROLE_ADMIN', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
                    $this->makeActionButton('depo_delete', array('id' => 'id'), 'ROLE_ADMIN', 'Delete', 'Delete', 'fa fa-trash-o', 'btn btn-default btn-xs delete-list-btn'),
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\Depo';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'depo_datatable';
    }
}
