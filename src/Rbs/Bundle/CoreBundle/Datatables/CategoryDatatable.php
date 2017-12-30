<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

/**
 * Class CategoryDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class CategoryDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('category_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('name', 'column', array('title' => 'Name',))
            ->add('bundles.name', 'array', array('title' => 'Modules', 'data' => 'bundles[, ].name'))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'actions' => array(
                    $this->makeActionButton('category_edit', array('id' => 'id'), 'ROLE_ADMIN', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
                    $this->makeActionButton('category_delete', array('id' => 'id'), 'ROLE_ADMIN', 'Delete', 'Delete', 'fa fa-trash-o', 'btn btn-default btn-xs delete-list-btn'),
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\Category';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'category_datatable';
    }
}
