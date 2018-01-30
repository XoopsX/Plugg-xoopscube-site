<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Developer_EditForm extends Plugg_FormController
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

        if (!$context->user->hasPermission('project developer edit') &&
            !$this->_developer->isOwnedBy($context->user)
        ) {
            // Roles higher or equal to the user may not be edited
            if ($this->_project->isDeveloper($context->user) < $this->_developer->get('role')) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/' . $this->_project->getId()
                ));

                return false;
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_developer->toHTMLQuickForm();
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Username'), h($this->_developer->User->getUsername())), 'role');
        if (!$context->user->hasPermission('project developer edit')) {
            $form->enableOnlyRolesLowerThan($context, $this->_project->isDeveloper($context->user), 'tasks');
        }

        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_developer->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_developer->applyForm($form);

        if ($this->_developer->commit()) {
            $context->response->setSuccess($context->plugin->_('Developer edited successfully'), array(
                'path' => '/' . $this->_project->getId(),
                'params' => array(
                    'view' => 'developers',
                    'developer_id' => $this->_developer->getId()
                ),
                'fragment' => 'developer' . $this->_developer->getId()
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