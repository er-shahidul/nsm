<?php
namespace Rbs\Bundle\SalesBundle\EventListener;

use Knp\Menu\MenuItem;
use Rbs\Bundle\CoreBundle\Event\ConfigureMenuEvent;
use Rbs\Bundle\CoreBundle\EventListener\ContextAwareListener;
use Rbs\Bundle\UserBundle\Entity\User;

class ConfigureMenuListener extends ContextAwareListener
{
    /**
     * @param ConfigureMenuEvent $event
     * @return MenuItem
     */
    public function onMenuConfigureMain(ConfigureMenuEvent $event)
    {
        /** @var MenuItem $menu */
        $menu = $event->getMenu();
        $menu->addChild('Sales', array('route' => ''))
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'fa fa-bar-chart-o')
            ->setLinkAttribute('data-hover', 'dropdown');

        $sp1 = false;
        $sp2 = false;
        $sp3 = false;
        $sp4 = false;
        $sp5 = false;

        if ($this->user->getUserType() != User::AGENT) {
            if ($this->authorizationChecker->isGranted(array('ROLE_DEPO_USER', 'ROLE_ORDER_VIEW', 'ROLE_ORDER_CREATE', 'ROLE_ORDER_EDIT', 'ROLE_ORDER_APPROVE', 'ROLE_ORDER_CANCEL', 'ROLE_CHICK_ORDER_MANAGE'))) {

                $sp1 = true;
                /** @var \Knp\Menu\MenuItem $menu2 */
                $menu2 = $menu['Sales']->addChild('Orders', array('route' => 'orders_home'))
                    ->setAttribute('icon', 'fa fa-th-list')
                    ->setChildrenAttribute('class', 'sub-menu');
                $menu2->addChild('All Orders', array('route' => 'orders_home'))->setAttribute('icon', 'fa fa-th-list');

                if ($this->authorizationChecker->isGranted(array('ROLE_CHICK_ORDER_MANAGE'))) {
                    $menu2->addChild('Manage Chick Order', array('route' => 'order_manage_chick'))->setAttribute('icon', 'fa fa-th-list');
                }

                if ($this->isMatch('order_create') || $this->isMatch('order_update') || $this->isMatch('order_details')) {
                    $menu['Sales']->getChild('Orders')->setCurrent(true);
                }

                if ($this->isMatch('orders_home')) {
                    $menu2['All Orders']->setCurrent(true);
                }
                if ($this->isMatch('order_manage_chick')) {
                    $menu2['Manage Chick Order']->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_ORDER_VIEW', 'ROLE_ORDER_CREATE', 'ROLE_ORDER_EDIT', 'ROLE_ORDER_APPROVE', 'ROLE_ORDER_CANCEL'))) {
                $sp1 = true;
                $menu['Sales']->addChild('Orders From SMS', array('route' => 'order_readable_sms'))
                    ->setAttribute('icon', 'fa fa-th-list');

            }
            if ($this->authorizationChecker->isGranted(array('ROLE_ORDER_VIEW'))) {
                $sp1 = true;
                $menu['Sales']->addChild('Unread SMS', array('route' => 'sms_home'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('sms')) {
                    $menu['Sales']->getChild('Unread SMS')->setCurrent(true);
                }
            }
            if ($sp1) {
                $menu['Sales']->addChild('', ['divider' => true]);
            }

        }
        if ($this->user->getUserType() == User::USER or $this->user->getUserType() == User::ZM) {
            if ($this->authorizationChecker->isGranted(array('ROLE_HEAD_OFFICE_USER', 'ROLE_PAYMENT_VIEW', 'ROLE_PAYMENT_CREATE', 'ROLE_PAYMENT_APPROVE', 'ROLE_PAYMENT_OVER_CREDIT_APPROVE'))) {
                $sp2 = true;
                $menu['Sales']->addChild('Payments', array('route' => 'payments_home'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('payment')) {
                    $menu['Sales']->getChild('Payments')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_CASH_RECEIVE_MANAGE'))) {
                $sp2 = true;
                $menu['Sales']->addChild('Cash Receive', array('route' => 'cash_receive_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('cash_receive_list') or $this->isMatch('cash_receive_create')) {
                    $menu['Sales']->getChild('Cash Receive')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_CASH_DEPOSIT_MANAGE'))) {
                $sp2 = true;
                $menu['Sales']->addChild('Cash Deposit', array('route' => 'cash_deposit_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('cash_deposit_list') or $this->isMatch('cash_deposit_create')) {
                    $menu['Sales']->getChild('Cash Deposit')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_HEAD_OFFICE_USER'))) {
                $sp2 = true;
                $menu['Sales']->addChild('Cash Receive From Depo', array('route' => 'cash_receive_from_depo_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('cash_receive_from_depo_list') or $this->isMatch('cash_receive_from_depo_details') or $this->isMatch('cash_receive_from_depo_receive_details')) {
                    $menu['Sales']->getChild('Cash Receive From Depo')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_INCENTIVE_MANAGE'))) {
                $sp2 = true;
                $menu['Sales']->addChild('Incentive', array('route' => 'incentives_home'))
                    ->setAttribute('icon', 'fa fa-th-list');
            }

            if ($sp2) {
                $menu['Sales']->addChild(str_repeat(' ', 2), ['divider' => true]);
            }
            
            if ($this->authorizationChecker->isGranted(array('ROLE_DELIVERY_MANAGE'))) {
                $sp3 = true;
                $menu['Sales']->addChild('Vehicle In/Out', array('route' => 'truck_info_in_out_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('truck_info_in_out_list')) {
                    $menu['Sales']->getChild('Vehicle In/Out')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_DELIVERY_MANAGE'))) {
                $sp3 = true;
                $menu['Sales']->addChild('Challan Add', array('route' => 'deliveries_home'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('deliveries_home')) {
                    $menu['Sales']->getChild('Challan Add')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_DELIVERY_MANAGE'))) {
                $sp3 = true;
                $menu['Sales']->addChild('Vehicle Load', array('route' => 'vehicle_info_load_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('vehicle_info_load_list')) {
                    $menu['Sales']->getChild('Vehicle Load')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_DELIVERY_MANAGE'))) {
                $sp3 = true;
                $menu['Sales']->addChild('Nourish Delivery', array('route' => 'vehicle_info_set_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('vehicle_info_set_list') or $this->isMatch('delivery_set') ) {
                    $menu['Sales']->getChild('Nourish Delivery')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_STOCK_VIEW', 'ROLE_STOCK_CREATE'))) {
                $sp3 = true;
                $menu['Sales']->addChild('Stocks', array('route' => 'stocks_home'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('stock')) {
                    $menu['Sales']->getChild('Stocks')->setCurrent(true);
                }
            }

            if ($sp3) {
                $menu['Sales']->addChild(str_repeat(' ', 3), ['divider' => true]);
            }
        }

        if ($this->user->getUserType() != User::AGENT) {
            if ($this->authorizationChecker->isGranted(array('ROLE_AGENT_VIEW', 'ROLE_AGENT_CREATE'))) {
                $sp4 = true;
                $menu['Sales']->addChild('Agents', array('route' => 'agents_home'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('agents_home') or $this->isMatch('agent_update') or $this->isMatch('agent_details') or
                    $this->isMatch('agent_groups_home') or $this->isMatch('agent_group_create') or $this->isMatch('agent_group_update')) {
                    $menu['Sales']->getChild('Agents')->setCurrent(true);
                }
                $menu['Sales']->addChild('Agents Bank', array('route' => 'agent_banks'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('agent_banks')) {
                    $menu['Sales']->getChild('Agents')->setCurrent(true);
                }
            }
        }
        if ($this->user->getUserType() == User::USER or $this->user->getUserType() == User::ZM) {
            if ($this->authorizationChecker->isGranted(array('ROLE_HEAD_OFFICE_USER', 'ROLE_AGENT_LEDGER_VIEW'))) {
                $sp4 = true;
                $menu['Sales']->addChild('Agents Ledger', array('route' => 'agents_laser'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('agents_laser')) {
                    $menu['Sales']->getChild('Agents Ledger')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_TRUCK_MANAGE'))) {
                $sp4 = true;
                $menu['Sales']->addChild('Delivered List', array('route' => 'truck_info_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('truck_info_list') or $this->isMatch('truck_info_add')) {
                    $menu['Sales']->getChild('Delivered List')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_HEAD_OFFICE_USER', 'ROLE_DAMAGE_GOODS_VERIFY', 'ROLE_DAMAGE_GOODS_APPROVE'))) {
                $sp4 = true;
                $menu['Sales']->addChild('Damage Goods', array('route' => 'damage_good_admin_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('damage_good_admin_list')) {
                    $menu['Sales']->getChild('Damage Goods')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_CREDIT_LIMIT_MANAGE'))) {
                $sp4 = true;
                $menu['Sales']->addChild('Credit Limit', array('route' => 'credit_limit_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('credit_limit_list') or $this->isMatch('credit_limit_add') or $this->isMatch('credit_limit_create') or $this->isMatch('credit_limit_notification_list')) {
                    $menu['Sales']->getChild('Credit Limit')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_BANK_SLIP_VERIFIER', 'ROLE_BANK_SLIP_APPROVAL'))) {
                $sp4 = true;
                $menu['Sales']->addChild('Bank Slip', array('route' => 'bank_info_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('bank_info_list')) {
                    $menu['Sales']->getChild('Bank Slip')->setCurrent(true);
                }
            }

            if ($sp4) {
                $menu['Sales']->addChild(str_repeat(' ', 4), ['divider' => true]);
            }

            if ($this->authorizationChecker->isGranted(array('ROLE_SWAPPING_MANAGE'))) {
                $sp5 = true;
                $menu['Sales']->addChild('RSM Swapping', array('route' => 'swapping_rsm_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('swapping_rsm_create') or $this->isMatch('swapping_rsm_list')) {
                    $menu['Sales']->getChild('RSM Swapping')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_SWAPPING_MANAGE'))) {
                $sp5 = true;
                $menu['Sales']->addChild('SR Swapping', array('route' => 'swapping_sr_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('swapping_sr_create') or $this->isMatch('swapping_sr_list')) {
                    $menu['Sales']->getChild('SR Swapping')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted(array('ROLE_TARGET_MANAGE'))) {
                $sp5 = true;
                $menu['Sales']->addChild('Target', array('route' => 'target_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('targets') or $this->isMatch('target_create') or $this->isMatch('target_update')) {
                    $menu['Sales']->getChild('Target')->setCurrent(true);
                }
            }
        }

        if ($sp5) {
            $menu['Sales']->addChild(str_repeat(' ', 5), ['divider' => true]);
        }

//        if ($this->authorizationChecker->isGranted(array('ROLE_SALES_REPORT'))) {
//            $menu['Sales']->addChild('District wise Item Report', array('route' => 'district_wise_item_monthly_report'))
//                ->setAttribute('icon', 'fa fa-th-list');
//        }
        
        if ($this->user->getUserType() == User::RSM and $this->authorizationChecker->isGranted(array('ROLE_RSM_GROUP'))){
            $menu['Sales']->addChild('RSM', array('route' => 'target_my'))
                ->setAttribute('icon', 'fa fa-th-list');
        }

        if ($this->user->getUserType() == User::SR and $this->authorizationChecker->isGranted(array('ROLE_SR_GROUP'))){
            $menu['Sales']->addChild('Damage Good', array('route' => 'damage_good_list'))
                ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('damage_good_list') or $this->isMatch('damage_good_form')) {
                    $menu['Sales']->getChild('Damage Good')->setCurrent(true);
                }
            $menu['Sales']->addChild('Chicken Set', array('route' => 'chicken_type_set_list'))
                ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('chicken_type_set_list') or $this->isMatch('chicken_type_set_list')) {
                    $menu['Sales']->getChild('Chicken Set')->setCurrent(true);
                }
        }

        if ($this->user->getUserType() == User::AGENT and $this->authorizationChecker->isGranted(array('ROLE_AGENT'))){
            $menu['Sales']->addChild('My Truck List', array('route' => 'truck_info_my_list'))
                ->setAttribute('icon', 'fa fa-th-list');
            if ($this->isMatch('truck_info_my_list') or $this->isMatch('truck_info_add')) {
                $menu['Sales']->getChild('My Truck List')->setCurrent(true);
            }

            $menu['Sales']->addChild('My Orders', array('route' => 'orders_my_home'))
                ->setAttribute('icon', 'fa fa-th-list');

            $menu['Sales']->addChild('Bank Slip', array('route' => 'bank_info_list'))
                ->setAttribute('icon', 'fa fa-th-list');
            if ($this->isMatch('bank_info_list') or $this->isMatch('orders_my_bank_info')) {
                $menu['Sales']->getChild('Bank Slip')->setCurrent(true);
            }

            $menu['Sales']->addChild('My Ledger', array('route' => 'my_laser'))
                ->setAttribute('icon', 'fa fa-th-list');

            $menu['Sales']->addChild('My Doc', array('route' => 'my_doc'))
                ->setAttribute('icon', 'fa fa-th-list');
        }

        return $menu;
    }
}