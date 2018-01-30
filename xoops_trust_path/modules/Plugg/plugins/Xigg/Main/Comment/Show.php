<?php
class Plugg_Xigg_Main_Comment_Show extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Remove node page info
        $context->response->popPageInfo();

        $this->forward($this->_application->comment->getVar('node_id'), $context);
    }
}