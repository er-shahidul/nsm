<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

/**
 * Class SubCategoryDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class SubCategoryDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('sub_category_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('subCategoryName', 'column', array('title' => 'Name',))
            ->add('category.name', 'column', array('title' => 'Parent Category',))
            ->add('head.username', 'column', array('title' => 'Head',))
            ->add('subHead.username', 'column', array('title' => 'SubHead',))
            ->add(null, 'action', array(
                'width' => '180px',
                'title' => 'Action',
                'actions' => array(
                    $this->makeActionButton('subcategory_edit', array('id' => 'id'), 'ROLE_ADMIN', 'Edit', 'Edit', 'fa fa-pencil-square-o'),
                    $this->makeActionButton('subcategory_delete', array('id' => 'id'), 'ROLE_ADMIN', 'Delete', 'Delete', 'fa fa-trash-o', 'btn btn-default btn-xs delete-list-btn'),
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\SubCategory';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'subcategory_datatable';
    }
}
