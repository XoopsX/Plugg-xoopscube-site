<?php
class Plugg_Project_Main_Project_ViewDetails extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$project = $this->getRequestedProject($context)) || !$project->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $this->_application->setData(array(
            'project' => $project,
            'is_developer' => $project->isDeveloper($context->user),
            'project_data_elements' => $context->plugin->getProjectFormDataElementDefinitions()
        ));
    }
}