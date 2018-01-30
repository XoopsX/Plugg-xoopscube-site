<?php
class Plugg_Message_User_Message extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', 'Plugg_Message_User_Message_', dirname(__FILE__) . '/Message');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($this->_application->message->title);
        return array(
            'reply'   => array(
                'controller' => 'Reply',
            ),
            'submit'   => array(
                'controller' => 'Submit',
                'callback' => true,
            ),
        );
    }
}