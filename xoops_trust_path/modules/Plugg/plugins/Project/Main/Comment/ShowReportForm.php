<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Comment_ShowReportForm extends Plugg_FormController
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
        $this->_submitable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        return $this->_abuse->toHTMLQuickForm('', $this->_application->createUrl(array(
            'path' => '/comment/' . $this->_comment->getId() . '/report')
        ));
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Report this comment'));
    }
}