<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Node_ShowCommentForm extends Plugg_FormController
{
    private $_comment;

    protected function _init(Sabai_Application_Context $context)
    {
        if (!$this->_application->node->allow_comments) {
            $context->response->setError($context->plugin->_('Comments not allowed for this news article'), array(
                'path' => '/' . $this->_application->node->getId(),
            ));
            return false;
        }
        $this->_comment = $this->_application->node->createComment();

        return true;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggShowCommentForm', array($context, $form, /*$isReply*/ true));
        $context->response->setPageInfo($context->plugin->_('Submit comment'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_comment->toHTMLQuickForm('', $this->_application->createUrl(array(
            'path' => '/' . $this->_application->node->getId() . '/comment'))
        );
        $form->removeElements(array('Node', 'body_html', 'allow_edit'));
        $form->setDefaults(array(
            'title' => 'Re: ' . trim($this->_application->node->title),
            'body' => "\n\n" . strtr("\n" . $this->_application->node->body, array("\n>" => "\n>>", "\n" => "\n> "))
        ));
        return $form;
    }
}