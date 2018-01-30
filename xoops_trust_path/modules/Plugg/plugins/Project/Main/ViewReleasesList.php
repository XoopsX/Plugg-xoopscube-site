<?php
class Plugg_Project_Main_ViewReleasesList extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $this->forward('releases', $context, true);

        // remove ViewProjects from the content names list
        $context->response->popContentName();
    }
}