<?php
class Plugg_User_Main_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ($context->user->isAuthenticated()) {
            $this->forward($context->user->getId(), $context);
            return;
        }
        $this->forward('login', $context);
    }
}