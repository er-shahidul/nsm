<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

/**
 * Class ProjectDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class ProjectDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('project_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('projectName', 'column', array('title' => 'Name',))
            ->add('address', 'column', array('title' => 'Address',))
            ->add('projectArea.areaName', 'column', array('title' => 'Project Area',))
            ->add('projectCategory.name', 'column', array('title' => 'Category',))
            ->add('bundles.name', 'array', array('title' => 'Modules', 'data' => 'bundles[, ].name'))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'actions' => array(
                    $this->makeActionButton('project_edit', array('id' => 'id'), 'ROLE_ADMIN', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
                    $this->makeActionButton('project_delete', array('id' => 'id'), 'ROLE_ADMIN', 'Delete', 'Delete', 'fa fa-trash-o', 'btn btn-default btn-xs delete-list-btn'),
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\Project';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'project_datatable';
    }
}
