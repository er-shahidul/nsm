<?php
namespace Rbs\Bundle\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use Rbs\Bundle\CoreBundle\Event\ConfigureMenuEvent;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder extends ContainerAware
{

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root')
            ->setChildrenAttributes(array(
                'class' => 'page-sidebar-menu',
                'data-keep-expanded' => 'false',
                'data-auto-scroll' => 'true',
                'data-slide-speed' => '200',

            ));
        $menu->addChild('Dashboard', array('route' => 'homepage'))->setAttribute('icon', 'fa fa-home');

        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::CONFIGURE_SALES_MENU, new ConfigureMenuEvent($factory, $menu));
        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::CONFIGURE_PURCHASE_MENU, new ConfigureMenuEvent($factory, $menu));
        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::CONFIGURE_INVENTORY_MENU, new ConfigureMenuEvent($factory, $menu));
        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::CONFIGURE_USER_MENU, new ConfigureMenuEvent($factory, $menu));
        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::CONFIGURE_SETTING_MENU, new ConfigureMenuEvent($factory, $menu));
        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::CONFIGURE_MAIN_MENU, new ConfigureMenuEvent($factory, $menu));

        return $menu;
    }

    /**
     * @param $menu
     * @param $title
     * @param $route
     * @param $icon
     * @return mixed
     */
    protected function addChildMenu(MenuItem $menu, $title, $route, $icon = 'icon-briefcase')
    {
        $menu
            ->addChild($title, array('route' => $route))
            ->setAttribute('icon', $icon);

        return $this;
    }
}