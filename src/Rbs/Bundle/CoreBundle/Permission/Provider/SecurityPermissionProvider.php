<?php

namespace Rbs\Bundle\CoreBundle\Permission\Provider;

use Rbs\Bundle\UserBundle\Permission\Provider\ProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SecurityPermissionProvider implements ProviderInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var Array */
    private $bundles;

    public function __construct($container)
    {
        $this->container = $container;
        $this->bundles = $this->container->getParameter('kernel.bundles');
    }

    public function getPermissions()
    {
        $roles = array();
        if (array_key_exists('RbsSalesBundle', $this->bundles)) {
            $roles = array_merge($roles, array(
                'ROLE_LOCATION_MANAGE', 'ROLE_AUDIT_LOG_VIEW', 'ROLE_CATEGORY_MANAGE',
                'ROLE_ITEM_MANAGE', 'ROLE_ITEM_TYPE_MANAGE', 'ROLE_DEPO_MANAGE',
                'ROLE_SALE_INCENTIVE_MANAGE', 'ROLE_TRANSPORT_INCENTIVE_MANAGE'
            ));
        }

        if (array_key_exists('RbsPurchaseBundle', $this->bundles)) {
            $roles = array_merge($roles, array(
                'ROLE_LOCATION_MANAGE', 'ROLE_AUDIT_LOG_VIEW', 'ROLE_CATEGORY_MANAGE',
                'ROLE_ITEM_MANAGE', 'ROLE_ITEM_TYPE_MANAGE', 'ROLE_DEPO_MANAGE',
                'ROLE_SALE_INCENTIVE_MANAGE', 'ROLE_TRANSPORT_INCENTIVE_MANAGE'
            ));
        }

        $roles = array_unique($roles);
        sort($roles);

        return array(
            'SYSTEM' => $roles
        );
    }
}