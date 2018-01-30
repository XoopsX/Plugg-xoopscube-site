<?php
class Plugg_Project_Main_ViewProjectsRSS extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $this->forward('', $context);

        // Remove ViewProjects from the content names list
        $context->response->popContentName();
        // Add ViewProjectsList to the content names list
        $context->response->pushContentName(strtolower(__CLASS__));

        $context->response->noLayout();
        $context->response->setContentStackLevel(1);
        $context->response->setCharset('UTF-8');
    }
}