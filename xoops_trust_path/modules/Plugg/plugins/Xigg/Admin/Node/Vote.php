<?php
class Plugg_Xigg_Admin_Node_Vote extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('List', 'Plugg_Xigg_Admin_Node_Vote_', dirname(__FILE__) . '/Vote');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            ':vote_id/edit' => array(
                'controller'   => 'Update',
                'requirements' => array(
                    ':vote_id' => '\d+'
                ),
                'title' => $context->plugin->_('Edit vote')
            ),
        );
    }
}