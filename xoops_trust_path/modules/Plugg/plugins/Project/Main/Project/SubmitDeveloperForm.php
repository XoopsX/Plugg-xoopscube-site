<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_SubmitDeveloperForm extends Plugg_FormController
{
    private $_project;
    private $_developer;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_project = $this->getRequestedProject($context)) ||
            !$this->_project->isReadable($context->user)
        ) {
            return false;
        }
        if ($this->_project->isDeveloper($context->user, false)) {
            $context->response->setError($context->plugin->_('You are already listed as a developer or have submitted a request'), array(
                'path' => '/' . $this->_project->getId()
            ));
            return false;
        }

        $this->_developer = $this->_project->createDeveloper();

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_developer->toHTMLQuickForm();
        $form->enableDeveloperHeader();
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_developer->applyForm($form);
        $this->_developer->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_developer->applyForm($form);
        $this->_developer->assignUser($context->user);
        if ($context->user->hasPermission('project developer approve')) {
            $this->_developer->setApproved();
        } else {
            $this->_developer->setPending();
        }
        $this->_developer->markNew();

        if ($this->_developer->commit()) {
            $message = $context->plugin->_('Developer request posted successfully.');
            if (!$this->_developer->isApproved()) {
                $message .= ' ' . $context->plugin->_('You will be listed on the project page once approved by the site administrators or the developers.');
            }
            $context->response->setSuccess($message, array(
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
        $context->response->setPageInfo($context->plugin->_('Submit developer request'));
    }
}