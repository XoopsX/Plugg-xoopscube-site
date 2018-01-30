<?php
class Plugg_Xigg_Admin_Node_Trackback extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('List', 'Plugg_Xigg_Admin_Node_Trackback_', dirname(__FILE__) . '/Trackback');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            ':trackback_id/edit' => array(
                'controller'   => 'Update',
                'requirements' => array(
                    ':trackback_id' => '\d+'
                ),
                'title' => $context->plugin->_('Edit trackback')
            ),
        );
    }
}