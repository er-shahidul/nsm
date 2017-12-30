<?php

namespace Rbs\Bundle\SalesBundle\Datatables;
use Rbs\Bundle\SalesBundle\Entity\DamageGood;

/**
 * Class DamageGoodDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class DamageGoodDatatable extends BaseDatatable
{
    public function getLineFormatter()
    {
        /** @var DamageGood $damageGood
         * @return mixed
         */
        $formatter = function($line){
            $damageGood = $this->em->getRepository('RbsSalesBundle:DamageGood')->find($line['id']);
            $line['isPathExist'] = !$damageGood->isPathExist();
            $line['isApproved'] = $damageGood->isApproved();
            $line['attachFile'] = empty($line['path']) ? '' : '<a href="'.$damageGood->getDownloadFilePath().'" rel="tooltip" title="view" class="btn btn-xs" role="button" target="_blank"><i class="fa fa-file"></i> View</a>';

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
            'url' => $this->router->generate('damage_good_list_ajax'),
            'type' => 'GET'
        ));

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->columnBuilder
            ->add('createdAt', 'datetime', array('title' => 'Date', 'date_format' => $dateFormat))
            ->add('user.username', 'column', array('title' => 'User'))
            ->add('agent.user.username', 'column', array('title' => 'Agent'))
            ->add('orderRef.id', 'column', array('title' => 'Order Number'))
            ->add('remark', 'column', array('title' => 'Remark'))
            ->add('status', 'column', array('title' => 'Status'))
            ->add('rejectReason', 'column', array('title' => 'Reason for Reject (if reject)'))
            ->add('amount', 'column', array('title' => 'Claim'))
            ->add('refundAmount', 'column', array('title' => 'Refund'))
            ->add('isPathExist', 'virtual', array('visible' => false))
            ->add('path', 'column', array('visible' => false))
            ->add('attachFile', 'virtual', array('title' => 'File'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\DamageGood';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'damage_good_datatable';
    }
}
