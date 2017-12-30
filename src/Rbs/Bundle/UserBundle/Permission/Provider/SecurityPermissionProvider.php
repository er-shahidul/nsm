<?php

namespace Rbs\Bundle\UserBundle\Permission\Provider;

class SecurityPermissionProvider implements ProviderInterface
{
    public function getPermissions()
    {
        return array(
            'USER' => array(
                'ROLE_USER_VIEW', 'ROLE_USER_CREATE', 'ROLE_ADMIN'
            ),

            'GROUP' => array(
                'ROLE_GROUP_VIEW', 'ROLE_GROUP_CREATE'
            ),
            
            'SALES' => array(
                'ROLE_RSM_GROUP', 'ROLE_SR_GROUP', 'ROLE_ZM_GROUP'
            ),

            'DAMAGE_GOODS' => array(
                'ROLE_DAMAGE_GOODS_VERIFY', 'ROLE_DAMAGE_GOODS_APPROVE'
            ),
            
        );
    }
}