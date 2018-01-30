<?php
class Plugg_User_Admin_Role extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', 'Plugg_User_Admin_Role_', dirname(__FILE__) . '/Role');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'add' => array(
                'controller' => 'Create',
            ),
            ':role_id/edit' => array(
                'controller' => 'Update',
                'access_callback' => array('_onAccess', '_isRoleEditable'),
                'requirements' => array(':role_id' => '\d+')
            ),
            ':role_id/delete' => array(
                'controller' => 'Delete',
                'access_callback' => array('_onAccess', '_isRoleEditable'),
                'requirements' => array(':role_id' => '\d+')
            ),
            ':role_id/member' => array(
                'controller' => 'Member',
                'access_callback' => '_onAccess',
                'requirements' => array(':role_id' => '\d+')
            ),
            ':role_id' => array(
                'controller' => 'Details',
                'requirements' => array(':role_id' => '\d+'),
                'access_callback' => '_onAccess',
            )
        );
    }

    function _onAccess($context, $controller)
    {
        if (!$role = $this->isValidEntityRequested($context, 'Role', 'role_id')) {
            return false;
        }
        $this->_application->setData(array(
            'role' => $role,
            'role_id' => $role->getId()
        ));

        return true;
    }

    function _isRoleEditable($context, $controller)
    {
        if ($this->_application->role->system) {
            $context->response->setError(
                $context->plugin->_('System defined roles may not be edited nor deleted'),
                array('base' => '/user/role')
            );
            return false;
        }

        return true;
    }
}