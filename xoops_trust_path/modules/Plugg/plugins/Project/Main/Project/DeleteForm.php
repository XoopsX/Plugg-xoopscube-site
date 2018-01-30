<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_DeleteForm extends Plugg_FormController
{
    var $_project;

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_confirmable = false;
        $this->_submitPhrase = $context->plugin->_('Delete');

        if ((!$this->_project = $this->getRequestedProject($context)) ||
            !$this->_project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project delete')) {
            $context->response->setError($context->plugin->_('Permission denied'), array(
                'path' => '/' . $this->_project->getId()
            ));
        }

        return true;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_project->markRemoved();

        if ($this->_project->commit()) {
            $context->response->setSuccess($context->plugin->_('Project deleted successfully'), array('path' => '/'));
            $this->_application->dispatchEvent('ProjectDeleteProjectSuccess', array($context, $this->_project));
            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Delete project'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_project->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Name'), h($this->_project->name));

        return $form;
    }
}