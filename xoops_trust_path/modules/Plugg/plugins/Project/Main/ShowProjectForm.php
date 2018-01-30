<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_ShowProjectForm extends Plugg_FormController
{
    private $_project;

    public function __construct()
    {
        $this->addFilter('isAuthenticated');
        $this->_submitable = false;
    }

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_project = $context->plugin->getModel()->create('Project');

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        return $this->getProjectForm($context, $this->_project);
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Submit project'));
        $this->_application->setData(array('project' => $this->_project));
    }
}