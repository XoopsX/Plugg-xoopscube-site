<?php
require_once 'SabaiXOOPS/GroupPermissionController.php';

class plugg_xoops_admin_roles extends SabaiXOOPS_GroupPermissionController
{
    public function __construct($module)
    {
        $options = array(
            'successMsg' => 'Roles assigned to groups successfully',
            'errorMsg' => 'Failed to assign roles to groups',
            'redirectURL' => XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/admin/roles.php'
        );
        parent::__construct($module, $options);
    }

    protected function _getRoles(Sabai_Application_Context $context)
    {
        $context->response->setPageInfo(_MD_A_PLUGG_HOME, array('script_alias' => 'admin'));
        $context->response->setPageInfo(_MD_A_PLUGG_XROLES);

        $role_list = array();
        foreach ($this->_application->getPlugin('user')->getModel()->Role->fetch() as $role) {
            $role_list[$role->getId()] = $role->name;
        }
        return $role_list;
    }
}