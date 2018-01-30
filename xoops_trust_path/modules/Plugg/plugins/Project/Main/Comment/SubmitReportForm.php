<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Comment_SubmitReportForm extends Plugg_FormController
{
    private $_abuse;
    private $_comment;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_comment = $this->getRequestedComment($context)) ||
            !$this->_comment->Project->isReadable($context->user)
        ) {
            return false;
        }

        $this->_abuse = $this->_comment->Project->createAbuse();

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        return $this->_abuse->toHTMLQuickForm();
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_abuse->applyForm($form);
        $this->_abuse->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_abuse->applyForm($form);
        $this->_abuse->assignUser($context->user);
        $this->_abuse->set('ip', getip());
        $this->_abuse->setEntity($this->_comment);
        $this->_abuse->setPending();
        $this->_abuse->markNew();
        if ($this->_abuse->commit()) {
            $context->response->setSuccess($context->plugin->_('Report posted successfully'), array(
                'path' => '/comment/' . $this->_comment->getId(),
                'params' => array('view' => 'comments'),
                'fragment' => 'comment' . $this->_comment->getId()
            ));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Report this comment'));
    }
}