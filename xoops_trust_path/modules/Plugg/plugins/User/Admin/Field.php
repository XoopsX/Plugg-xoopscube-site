<?php
class Plugg_User_Admin_Field extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', 'Plugg_User_Admin_Field_', dirname(__FILE__) . '/Field');
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