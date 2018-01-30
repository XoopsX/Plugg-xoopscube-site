<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_ShowCommentForm extends Plugg_FormController
{
    private $_project;
    private $_comment;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_project = $this->getRequestedProject($context)) ||
            !$this->_project->isReadable($context->user) ||
            !$this->_project->get('allow_comments')
        ) {
            return false;
        }

        $this->_comment = $this->_project->createComment();
        $this->_submitable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_comment->toHTMLQuickForm('', $this->_application->createUrl(array(
            'path' => '/' . $this->_project->getId() . '/comment/submit'
        )), 'post');
        $form->removeElements(array('allow_edit'));
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add comment'));
    }
}