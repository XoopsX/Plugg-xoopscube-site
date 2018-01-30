<?php
class Plugg_Project_Main_Project_ViewDevelopers extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$project = $this->getRequestedProject($context)) || !$project->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $is_developer = $project->isDeveloper($context->user);
        if (!$context->user->hasPermission('project developer approve') && !$is_developer) {
            $developers = $context->plugin->getModel()->Developer
                ->criteria()
                ->status_is(Plugg_Project_Plugin::DEVELOPER_STATUS_APPROVED)
                ->fetchByProject($project->getId(), 0, 0, 'developer_role', 'DESC');
            unset($criteria);
        } else {
            $developers = $context->plugin->getModel()->Developer->fetchByProject($project->getId(), 0, 0, 'developer_role', 'DESC');
        }

        $this->_application->setData(array(
            'project' => $project,
            'is_developer' => $is_developer,
            'developers' => $developers
        ));
    }
}