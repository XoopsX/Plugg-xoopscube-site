<?php
class Plugg_Project_Main_Release_ViewReportsRSS extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$release = $this->getRequestedRelease($context)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $this->forward('reports', $context, true);

        // remove ViewProjects from the content names list
        $context->response->popContentName();

        $context->response->noLayout();
        $context->response->setCharset('UTF-8');
    }
}