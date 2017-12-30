<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

/**
 * Class TargetDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class TargetDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        $formatter = function($line){
            $line["monthDiff"] = $this->monthDifference($line['startDate'], $line['endDate']);
            return $line;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('target_list_ajax'),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('zilla.name', 'column', array('title' => 'Zilla'))
            ->add('upozilla.name', 'column', array('title' => 'Upozilla'))
            ->add('quantity', 'column', array('title' => 'Quantity'))
            ->add('category.name', 'column', array('title' => 'Category'))
            ->add('startDate', 'datetime', array('title' => 'Start Date', 'date_format' => $dateFormat))
            ->add('endDate', 'datetime', array('title' => 'End Date', 'date_format' => $dateFormat))
            ->add('monthDiff', 'virtual', array('title' => 'Month'))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'Update',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'target_update',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Edit',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'edit-action',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        )
                    )
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\Target';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'target_datatable';
    }

    public function monthDifference($start, $end)
    {
        $difference = $start->diff($end); // $difference->y // $difference->m // $difference->d

        return $difference->m + 1 ;
    }
}
