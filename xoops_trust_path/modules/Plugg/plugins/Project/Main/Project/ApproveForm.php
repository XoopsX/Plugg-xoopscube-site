<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_ApproveForm extends Plugg_FormController
{
    var $_project;

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_confirmable = false;
        $this->_submitPhrase = $context->plugin->_('Approve');

        if ((!$this->_project = $this->getRequestedProject($context)) ||
            $this->_project->isApproved()
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project approve')) {
            $context->response->setError($context->plugin->_('Permission denied'), array('path' => '/'));
            return false;
        }

        return true;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_project->setApproved();

        // need to use the model object here to commit pending entities together
        if ($context->plugin->getModel()->commit()) {
            // reload project
            if ($this->_project->reload()->updateLatestRelease()) {
                $context->response->addMessage($context->plugin->_('Project latest release updated'));
            }
            $context->response->setSuccess($context->plugin->_('Project approved successfully'), array(
                'path' => '/' . $this->_project->getId()
            ));
            $this->_application->dispatchEvent('ProjectSubmitProjectSuccess', array($context, $this->_project, /*$isEdit*/ false));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Approve project'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_project->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Name'), h($this->_project->get('name')));
        $form->addElement('static', '', $context->plugin->_('Summary'), $this->_project->get('summary_html'));
        $form->addElement('static', '', $context->plugin->_('Submitter'), $this->_project->User->getUsername());

        return $form;
    }
}