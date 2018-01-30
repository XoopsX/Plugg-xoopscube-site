<?php
class Plugg_User_Admin_Autologin extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', 'Plugg_User_Admin_Autologin_', dirname(__FILE__) . '/Autologin');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array('controller' => 'Submit'),
        );
    }
}