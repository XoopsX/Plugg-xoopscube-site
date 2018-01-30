<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_ShowLinkForm extends Plugg_FormController
{
    private $_project;
    private $_link;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_project = $this->getRequestedProject($context)) ||
            !$this->_project->isReadable($context->user) ||
            !$this->_project->get('allow_links')
        ) {
            return false;
        }

        $this->_link = $this->_project->createLink();
        $this->_submitable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_link->toHTMLQuickForm('', $this->_application->createUrl(array(
            'path' => '/' . $this->_project->getId() . '/link/submit')
        ), 'post');
        $form->removeElements(array('allow_edit'));
        $form->setDefaults(array(
            'url' => 'http://'
        ));
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add link'));
    }
}