<?php
$cache = $context->plugin->getCache();
if (!$cached = $cache->get('permissions')) {
    $permissions = $permissions_default = array();
    $this->_application->dispatchEvent('UserAdminRolePermissions', array(&$permissions));
    $this->_application->dispatchEvent('UserAdminRolePermissionsDefault', array(&$permissions_default));
    ksort($permissions, SORT_LOCALE_STRING);
    $cache->save(serialize(array($permissions, $permissions_default)), 'permissions');
} else {
    list($permissions, $permissions_default) = unserialize($cached);
}