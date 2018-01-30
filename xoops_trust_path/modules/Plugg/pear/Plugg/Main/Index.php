<?php
class Plugg_Main_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        static $done = false;
        // Prevent recursive routing
        if (!$done) {
            if ($plugin = trim($this->_application->getConfig('defaultPlugin'), '/')) {
                $done = true;
                $this->forward('/' . strtolower($plugin), $context);
                return;
            }
        }
    }
}