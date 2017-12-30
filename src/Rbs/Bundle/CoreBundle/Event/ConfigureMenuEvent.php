<?php

namespace Rbs\Bundle\CoreBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{
    const CONFIGURE_MAIN_MENU = 'core_bundle.menu_main';
    const CONFIGURE_SALES_MENU = 'core_bundle.menu_sales';
    const CONFIGURE_PURCHASE_MENU = 'core_bundle.menu_purchase';
    const CONFIGURE_INVENTORY_MENU = 'core_bundle.menu_inventory';
    const CONFIGURE_SETTING_MENU = 'core_bundle.menu_setting';
    const CONFIGURE_USER_MENU = 'core_bundle.menu_user';

    private $factory;
    private $menu;
    private $parent;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     * @param null|string $parent Key of the parent item
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, $parent = null)
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->parent = $parent;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }
}