<?php
class Plugg_User_Admin_Role_Member extends Plugg_RoutingController
{
    function Plugg_User_Admin_Role_Member()
    {
        parent::__construct('List', 'Plugg_User_Admin_Role_Member_', dirname(__FILE__) . '/Member');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            'add' => array(
                'controller' => 'Create',
            ),
            ':member_id/remove' => array(
                'controller' => 'Delete',
                'requirements' => array(':member_id' => '\d+'),
                'access_callback' => '_onAccess'
            )
        );
    }

    function _onAccess($context, $controller)
    {
        if (!$this->isValidEntityRequested($context, 'Member')) {
            return false;
        }

        return true;
    }
}