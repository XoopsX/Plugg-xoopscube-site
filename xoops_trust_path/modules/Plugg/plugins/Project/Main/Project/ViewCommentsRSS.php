<?php
class Plugg_Project_Main_Project_ViewCommentsRSS extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$project = $this->getRequestedProject($context)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $this->forward('comments', $context, true);

        // remove ViewProjects from the content names list
        $context->response->popContentName();

        $context->response->noLayout();
        $context->response->setCharset('UTF-8');
    }
}