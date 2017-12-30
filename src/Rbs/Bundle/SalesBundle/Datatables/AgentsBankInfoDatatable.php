<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\AgentsBankInfo;

/**
 * Class AgentsBankInfoDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class AgentsBankInfoDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var AgentsBankInfo $agentsBankInfo
         * @return mixed
         */
        $formatter = function($line){
            $agentsBankInfo = $this->em->getRepository('RbsSalesBundle:AgentsBankInfo')->find($line['id']);
            $line['isPathExist'] = !$agentsBankInfo->isPathExist();

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

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('bank_info_list_ajax'),
            'type' => 'GET'
        ));

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->columnBuilder
            ->add('createdAt', 'datetime', array('title' => 'Date', 'date_format' => $dateFormat))
            ->add('orderRef.id', 'column', array('title' => 'Order Number'))
            ->add('bankName', 'column', array('title' => 'Bank Name'))
            ->add('branchName', 'column', array('title' => 'Branch Name'))
            ->add('remark', 'column', array('title' => 'Remark'))
            ->add('amount', 'column', array('title' => 'Amount'))
            ->add('status', 'column', array('title' => 'Status'))
            ->add('isPathExist', 'virtual', array('visible' => false))
            ->add('path', 'column', array('visible' => false))
            ->add(null, 'action', array(
                'width' => '200px',
                'title' => 'File',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'agent_bank_info_doc_view',
                        'route_parameters' => array(
                            'path' => 'path'
                        ),
                        'label' => 'View',
                        'icon' => 'fa fa-file',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'view',
                            'class' => 'btn btn-xs',
                            'role' => 'button',
                            'target'=> '_blank'
                        ),
                        'render_if' => array('isPathExist')
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
        return 'Rbs\Bundle\SalesBundle\Entity\AgentsBankInfo';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'agents_bank_info_datatable';
    }
}
