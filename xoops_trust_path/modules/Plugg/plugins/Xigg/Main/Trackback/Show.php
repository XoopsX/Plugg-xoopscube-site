<?php
class Plugg_Xigg_Main_Trackback_Show extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $this->forward($this->_application->trackback->getVar('node_id'), $context);
    }
}