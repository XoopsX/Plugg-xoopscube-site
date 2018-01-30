<?php
class Plugg_User_Model_Role extends Plugg_User_Model_Base_Role
{
    function getPermissions()
    {
        if ($permissions = $this->get('permissions')) {
            return explode('|', $permissions);
        }
        return array();
    }

    function setPermissions($permsArr)
    {
        $this->set('permissions', implode('|', $permsArr));
    }
}

class Plugg_User_Model_RoleRepository extends Plugg_User_Model_Base_RoleRepository
{
}