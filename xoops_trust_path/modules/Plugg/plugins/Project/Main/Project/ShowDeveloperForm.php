<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_ShowDeveloperForm extends Plugg_FormController
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
        $this->_submitable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_developer->toHTMLQuickForm('', $this->_application->createUrl(array(
            'path' => '/' . $this->_project->getId() . '/developer/submit')
        ));
        $form->enableDeveloperHeader();
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Submit developer request'));
    }
}