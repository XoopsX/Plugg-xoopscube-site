<?php
class Plugg_Project_Main_ViewLinksList extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $this->forward('links', $context, true);

        // remove ViewProjects from the content names list
        $context->response->popContentName();
    }
}