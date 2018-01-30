<?php
require_once 'Sabai/Application/ControllerFilter.php';

class plugg_xoops_permission_filter extends Sabai_Application_ControllerFilter
{
    public function before(Sabai_Application_Context $context, Sabai_Application $application)
    {
        if (empty($GLOBALS['xoopsUser'])
            || $context->user->isFinalized()
            || $context->user->isSuperUser()
        ) {
            return;
        }

        $xoops_groups = $GLOBALS['xoopsUser']->getGroups();
        // Set as super user if belongs to the default admin group
        if (in_array(XOOPS_GROUP_ADMIN, $xoops_groups)) {
            $context->user->setSuperUser(true);
            $context->user->finalize();
            return;
        }

        // Set as super user if module admin
        $module_id = $GLOBALS['xoopsModule']->getVar('mid');
        if (xoops_gethandler('groupperm')->checkRight('module_admin', $module_id, $xoops_groups)) {
            $context->user->setSuperUser(true);
            $context->user->finalize();
            return;
        }

        // Load roles and set permissions
        $perm = $GLOBALS['xoopsModule']->getVar('dirname') . '_role';
        if ($role_ids = xoops_gethandler('groupperm')->getItemIds($perm, $xoops_groups, $module_id)) {
            $roles = $application->getPlugin('user')->getModel()->Role
                ->fetchByCriteria(Sabai_Model_Criteria::createIn('role_id', $role_ids));
            foreach ($roles as $role) {
                foreach ($role->getPermissions() as $perm) {
                    $context->user->addPermission($perm);
                }
            }
        }

        $context->user->finalize();
    }

    public function after(Sabai_Application_Context $context, Sabai_Application $application){}
}