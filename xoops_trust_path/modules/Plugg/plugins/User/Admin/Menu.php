<?php
class Plugg_User_Admin_Menu extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', 'Plugg_User_Admin_Menu_', dirname(__FILE__) . '/Menu');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit'
            )
        );
    }
}