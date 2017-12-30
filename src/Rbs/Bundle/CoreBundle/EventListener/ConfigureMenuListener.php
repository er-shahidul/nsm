<?php
namespace Rbs\Bundle\CoreBundle\EventListener;

use Knp\Menu\MenuItem;
use Rbs\Bundle\CoreBundle\Event\ConfigureMenuEvent;
use Rbs\Bundle\UserBundle\Entity\User;

class ConfigureMenuListener extends ContextAwareListener
{
    /**
     * @param ConfigureMenuEvent $event
     * @return MenuItem
     */
    public function onMenuConfigureMain(ConfigureMenuEvent $event)
    {
        if ($this->user->getUserType() == User::USER or $this->user->getUserType() == User::ZM) {
            /** @var MenuItem $menu */
            $menu = $event->getMenu();

            if ($this->authorizationChecker->isGranted(array('ROLE_AGENT'))) {
                return $menu;
            }

            $menu->addChild('Settings', array())
                ->setAttribute('dropdown', true)
                ->setAttribute('icon', 'fa fa-cog')
                ->setLinkAttribute('data-hover', 'dropdown');


            if ($this->authorizationChecker->isGranted('ROLE_ITEM_MANAGE')) {
                $menu['Settings']->addChild('Items', array('route' => 'item'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('item') && !$this->isMatch('itemtype')) {
                    $menu['Settings']->getChild('Items')->setCurrent(true);
                }
            }

            /*if ($this->authorizationChecker->isGranted('ROLE_ITEM_TYPE_MANAGE')) {
                $menu['Settings']->addChild('Item Types', array('route' => 'itemtype'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('itemtype')) {
                    $menu['Settings']->getChild('Item Types')->setCurrent(true);
                }
            }*/

            if ($this->authorizationChecker->isGranted('ROLE_ITEM_MANAGE')) {
                $menu['Settings']->addChild('Categories', array('route' => 'category'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('category')) {
                    $menu['Settings']->getChild('Categories')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_LOCATION_MANAGE')) {
                $menu['Settings']->addChild('Locations', array('route' => 'locations'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('locations')) {
                    $menu['Settings']->getChild('Locations')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_HEAD_OFFICE_USER')) {
                $menu['Settings']->addChild('Chicken Set', array('route' => 'chicken_set_in_location'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('chicken_set_in_location')) {
                    $menu['Settings']->getChild('Chicken Set')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_DEPO_MANAGE')) {
                $menu['Settings']->addChild('Depot', array('route' => 'depo'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('depo')) {
                    $menu['Settings']->getChild('Depot')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_SALE_INCENTIVE_MANAGE')) {
                $menu['Settings']->addChild('Sales Incentive', array('route' => 'sale_incentive_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('sale_incentive_list') or $this->isMatch('sale_incentive_import')) {
                    $menu['Settings']->getChild('Sales Incentive')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_TRANSPORT_INCENTIVE_MANAGE')) {
                $menu['Settings']->addChild('Transport Commission', array('route' => 'transport_incentive_list'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('transport_incentive_list') or $this->isMatch('transport_incentive_import')) {
                    $menu['Settings']->getChild('Transport Commission')->setCurrent(true);
                }
            }


            if ($this->authorizationChecker->isGranted('ROLE_HEAD_OFFICE_USER')) {
                /** @var \Knp\Menu\MenuItem $menu2 */
                $menu2 = $menu['Settings']->addChild('Bank Accounts', array('route' => 'bank_account'))
                    ->setAttribute('icon', 'fa fa-th-list')
                    ->setChildrenAttribute('class', 'sub-menu');
                $menu2->addChild('Accounts', array('route' => 'bank_account'))->setAttribute('icon', 'fa fa-th-list');
                $menu2->addChild('Banks', array('route' => 'bank'))->setAttribute('icon', 'fa fa-th-list');
                $menu2->addChild('Branches', array('route' => 'bankbranch'))->setAttribute('icon', 'fa fa-th-list');

                if ($this->isMatch('bank_create') || $this->isMatch('bank_update')) {
                    $menu2['Banks']->setCurrent(true);
                }
                if ($this->isMatch('bankbranch_create') || $this->isMatch('bankbranch_update')) {
                    $menu2['Branches']->setCurrent(true);
                }
                if ($this->isMatch('bank_account_create') || $this->isMatch('bank_account_update')) {
                    $menu2['Accounts']->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $menu['Settings']->addChild('SMS Emulator', array('route' => 'order_via_sms'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('order_via_sms')) {
                    $menu['Settings']->getChild('SMS Emulator')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $menu['Settings']->addChild('Agent Bank Info Sent', array('route' => 'agent_bank_info_sms'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('agent_bank_info_sms') || $this->isMatch('agent_bank_list_sms')) {
                    $menu['Settings']->getChild('Agent Bank Info Sent')->setCurrent(true);
                }
            }

            /* Report Menu*/
            $menu->addChild('Report', array())
                ->setAttribute('dropdown', true)
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setLinkAttribute('data-hover', 'dropdown');

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $menu['Settings']->addChild('Vehicle SMS Emulator', array('route' => 'vehicle_via_sms'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('vehicle_via_sms')) {
                    $menu['Settings']->getChild('Vehicle SMS Emulator')->setCurrent(true);
                }
            }

            if (empty($menu->getChild('Settings')->getChildren())) {
                $menu->removeChild($menu['Settings']);
            }

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $menu['Report']->addChild('Upazilla Wise Report', array('route' => 'upozilla_wise_item_report'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('upozilla_wise_item_report')) {
                    $menu['Report']->getChild('Upazilla Wise Report')->setCurrent(true);
                }
            }
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $menu['Report']->addChild('Item Yearly Report', array('route' => 'item_yearly_report'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('item_yearly_report')) {
                    $menu['Report']->getChild('Item Yearly Report')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_SALES_REPORT')) {
                $menu['Report']->addChild('Payment Report', array('route' => 'report_payment'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('report_payment')) {
                    $menu['Report']->getChild('Payment Report')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted('ROLE_SALES_REPORT')) {
                $menu['Report']->addChild('Stock Report', array('route' => 'report_stock'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('report_stock')) {
                    $menu['Report']->getChild('Stock Report')->setCurrent(true);
                }
            }

            if (empty($menu->getChild('Report')->getChildren())) {
                $menu->removeChild($menu['Report']);
            }

            return $menu;
        }
    }
}