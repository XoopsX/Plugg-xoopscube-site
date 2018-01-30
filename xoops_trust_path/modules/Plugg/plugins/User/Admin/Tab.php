<?php
class Plugg_User_Admin_Tab extends Plugg_RoutingController
{
    function Plugg_User_Admin_Tab()
    {
        parent::__construct('List', 'Plugg_User_Admin_Tab_', dirname(__FILE__) . '/Tab');
    }

    function _doGetRoutes(Sabai_Application_Context $context)
    {
        $context->response->setCurrentTab('tab');
        return array('submit' => array('controller' => 'Submit'));
    }
}