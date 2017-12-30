<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\UserBundle\Entity\Profile;
use Rbs\Bundle\UserBundle\Entity\User;

/**
 * Class PaymentDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class PaymentDatatable extends BaseDatatable
{
    private $user;
    protected $allowAgentSearch;

    public function getLineFormatter()
    {
        /** @var Profile $profile
        /** @var Payment $payment
         * @return mixed
         */
        $formatter = function($line){
            $payment = $this->em->getRepository('RbsSalesBundle:Payment')->find($line['id']);
            $line['bankInfo'] = $this->formatBankInfo($line['bankAccount'], $line['transactionType'], $line['paymentMethod']);
            $line['agentBankInfo'] = $payment->getAgentBankBranch()?$payment->getAgentBankBranch()->getBank().', '.$payment->getAgentBankBranch()->getBranch():"";
            $line['totalAmount'] = '<div style="text-align: right;">'. number_format($line['amount'], 2) .'</div>';
            $line['depositedAmount'] = '<div style="text-align: right;">'. number_format($payment->getDepositedAmount(), 2) .'</div>';
            if ($this->allowAgentSearch) {
                //$line["fullName"] = $this->resolveAgentName($line['agent']);
                $line["fullName"] = $payment->getAgent()->getIdName();
            }

            $line['remarkText'] = $line['transactionType'] == Payment::CR ? $line['remark'] : '';
            $line["actionButtons"] = $this->generateActionList($payment);

            return $line;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        /** @var User $user */
        $this->user = $this->securityToken->getToken()->getUser();
        $this->allowAgentSearch = $this->user->getUserType() != User::AGENT;

        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions(array_merge($this->defaultOptions(), array(
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order' => [[0, 'desc']],
        )));

        $this->callbacks->setCallbacks(array
            (
                'init_complete' => "function( settings ) {
                        Payment.filterInit({$this->allowAgentSearch});
                }"
            )
        );

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('payment_list_ajax'),
            'type' => 'GET'
        ));

        $twigVars = $this->twig->getGlobals();
        $dateFormat = isset($twigVars['js_moment_date_format']) ? $twigVars['js_moment_date_format'] : 'D-MM-YY';

        $this->columnBuilder->add('createdAt', 'datetime', array( 'title' => 'Date', 'date_format' => $dateFormat ));
        if ($this->allowAgentSearch) {
            //$this->columnBuilder->add('agent.user.id', 'column', array('title' => 'Agent Name', 'render' => 'resolveAgentName'));
        }

        $this->columnBuilder
            ->add('fullName', 'virtual', array('visible' => true, 'title' => 'Agent Name'))
            ->add('depositedAmount', 'virtual', array('title' => 'Deposited Amount'))
            ->add('totalAmount', 'virtual', array('title' => 'Actual Amount'))
            ->add('paymentMethod', 'column', array('visible' => false))
            ->add('agentBankInfo', 'virtual', array('title' => 'Sender Bank'))
            ->add('bankInfo', 'virtual', array('title' => 'Receive Bank'))
            ->add('orders.id', 'array', array(
                'title' => 'Orders',
                'data' => 'orders[, ].id'
            ))
            ->add('remarkText', 'virtual', array('title' => 'Remarks'))
            ->add('remark', 'column', array('visible' => false))
            ->add('transactionType', 'column', array('visible' => false))
            ->add('bankAccount.id', 'column', array('visible' => false))
            ->add('amount', 'column', array('visible' => false))
            ->add('agent.user.id', 'column', array('visible' => false))
            ->add('actionButtons', 'virtual', array('title' => 'Action'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\Payment';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'payment_datatable';
    }

    private function formatBankInfo($bankAccount, $transactionType, $paymentMethod)
    {
        if ($transactionType == Payment::DR) return '';

        $data = '';

        if (!empty($bankAccount)) {
            $bankAccount = $this->em->getRepository('RbsCoreBundle:BankAccount')->find($bankAccount['id']);
            if ($bankAccount) {
                $data .= $bankAccount->getCode();
            }
        }

        return $data;
    }

    private function resolveAgentName($agent)
    {
        $profile = $this->em->getRepository('RbsUserBundle:Profile')->findOneBy(array('user' => $agent['user']['id']));

        return Agent::agentIdNameFormat($agent['id'], $profile->getFullName());
    }

    private function generateActionList(Payment $payment)
    {
//        $html = '<div class="actions">
//                <div class="btn-group">
//                    <a class="btn btn-sm btn-default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
//                    Actions <i class="fa fa-angle-down"></i>
//                    </a>
//                    <ul class="dropdown-menu pull-right">
//                    ';
//
//        $html .='</ul>
//                </div>
//            </div>';
        $html = sprintf('<a href="%s" rel="tooltip" title="show-action" class="btn" role="button" data-target="#ajaxAmountEdit" data-toggle="modal"> <i class="i"></i> %s </a>', $this->router->generate('payment_edit', array('id'=> $payment->getId())), 'Amount Edit');

        return $html;
    }
}
