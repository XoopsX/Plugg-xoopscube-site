<?php
class Plugg_Xigg_Main_Trackback extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('Show', __CLASS__ . '_', dirname(__FILE__) . '/Trackback');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'delete' => array(
                'controller'   => 'DeleteForm',
                'requirements' => array(':trackback_id' => '\d+'),
            ),
            'edit' => array(
                'controller'   => 'EditTrackbackForm',
                'requirements' => array(':trackback_id' => '\d+'),
            ),
        );
    }
}