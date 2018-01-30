<?php
class Plugg_User_Admin_Auth_Authdata extends Plugg_RoutingController
{
    function Plugg_User_Admin_Auth_Authdata()
    {
        parent::__construct('List', 'Plugg_User_Admin_Auth_Authdata_', dirname(__FILE__) . '/Authdata');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            ':authdata_id/remove' => array(
                'controller' => 'Delete',
                'requirements' => array(':authdata_id' => '\d+'),
                'access_callback' => '_onAccess'
            )
        );
    }

    protected function _onAccess($context, $controller)
    {
        if (!$this->isValidEntityRequested($context, 'Authdata'))
        {
            return false;
        }

        return true;
    }
}