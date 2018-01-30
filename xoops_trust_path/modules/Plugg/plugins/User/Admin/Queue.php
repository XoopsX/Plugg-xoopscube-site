<?php
class Plugg_User_Admin_Queue extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', 'Plugg_User_Admin_Queue_', dirname(__FILE__) . '/Queue');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            ':queue_id/send' => array(
                'controller' => 'Send',
                'requirements' => array(':queue_id' => '\d+')
            ),
            'submit' => array('controller' => 'Submit')
        );
    }
}