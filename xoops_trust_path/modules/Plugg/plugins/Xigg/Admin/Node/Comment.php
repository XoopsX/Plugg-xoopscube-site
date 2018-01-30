<?php
class Plugg_Xigg_Admin_Node_Comment extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('List', 'Plugg_Xigg_Admin_Node_Comment_', dirname(__FILE__) . '/Comment');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            ':comment_id/edit' => array(
                'controller'   => 'Update',
                ':comment_id' => '\d+',
            )
        );
    }
}