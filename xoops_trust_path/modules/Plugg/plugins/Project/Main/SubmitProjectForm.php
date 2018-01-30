<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_SubmitProjectForm extends Plugg_FormController
{
    private $_project;

    public function __construct()
    {
        $this->addFilter('isAuthenticated');
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

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_project->applyForm($form);
        $this->_project->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_project->applyForm($form);
        $this->_project->assignUser($context->user);
        if ($context->user->hasPermission('project release approve')) {
            $this->_project->setApproved();
        } else {
            $this->_project->setPending();
        }
        // Set the release latest time to the current time
        $this->_project->set('lastupdate', time());
        $this->_project->markNew();
        if ($context->plugin->getModel()->commit()) {
            $message = $context->plugin->_('Project submitted successfully.');
            if ($this->_project->isApproved()) {
                $url = array('path' => '/' . $this->_project->getId());
                $this->_application->dispatchEvent('ProjectSubmitProjectSuccess', array($context, $this->_project, /*$isEdit*/ false));
            } else {
                $message .= ' ' . $context->plugin->_('It will be listed on the project page once approved by the site administrators.');
                $url = array('path' => '/');
            }
            $context->response->setSuccess($message, $url);

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Submit project'));
        $this->_application->setData(array('project' => $this->_project));
    }
}