<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Developer_DeleteForm extends Plugg_FormController
{
    private $_project;
    private $_developer;

    protected function _init(Sabai_Application_Context $context)
    {
        if (!$this->_developer = $this->getRequestedDeveloper($context)) {
            return false;
        }

        if ((!$this->_project = $this->_developer->Project) ||
            !$this->_project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project developer delete')) {
            // is the user a developer and allowed to delete?
            if ($this->_project->isDeveloper($context->user) < $this->_developer->get('role')) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/' . $this->_project->getId()
                ));

                return false;
            }
        }

        $this->_confirmable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_developer->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Username'), h($this->_developer->User->getUsername()));
        $form->addElement('static', '', $context->plugin->_('Role'), h($this->_developer->getRoleStr()));
        $form->addElement('static', '', $context->plugin->_('Tasks'), h($this->_developer->get('tasks')));

        return $form;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_developer->markRemoved();

        if ($this->_developer->commit()) {
            $context->response->setSuccess($context->plugin->_('Developer deleted successfully'), array(
                'path' => '/' . $this->_project->getId(),
                'params' => array('view' => 'developers')
            ));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Edit developer'));
    }
}