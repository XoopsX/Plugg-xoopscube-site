<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_ShowReleaseForm extends Plugg_FormController
{
    private $_project;
    private $_release;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_project = $this->getRequestedProject($context)) ||
            !$this->_project->isReadable($context->user) ||
            !$this->_project->get('allow_releases')
        ) {
            return false;
        }

        $this->_release = $this->_project->createRelease();
        $this->_submitable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_release->toHTMLQuickForm('', $this->_application->createUrl(array(
            'path' => '/' . $this->_project->getId() . '/release/submit')
        ), 'post');
        if (!$this->_project->isDeveloper($context->user)) {
            if (!$context->user->hasPermission('project release allow download')) {
                $form->removeElement('allow_download');
            }
            if (!$context->user->hasPermission('project release allow reports')) {
                $form->removeElement('allow_reports');
            }
        }
        $form->removeElements(array('allow_edit'));
        $form->setDefaults(array(
            'download_url' => 'http://'
        ));
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add release'));
    }
}