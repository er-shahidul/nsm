<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class AuditLogDatatable
 *
 * @package Rbs\Bundle\CoreBundle\Datatables
 */
class AuditLogDatatable extends BaseDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions(array_merge($this->defaultOptions(), array(
            'page_length' => 50,
            'order' => [[0, 'desc']]
        )));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('audit_log_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('eventTime', 'datetime', array('title' => 'Event Time',))
            ->add('type', 'column', array('title' => 'Type',))
            ->add('description', 'column', array('title' => 'Description',))
            ->add('user', 'column', array('title' => 'User',))
            ->add('ip', 'column', array('title' => 'Ip',));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\AuditLog';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'auditlog_datatable';
    }
}
