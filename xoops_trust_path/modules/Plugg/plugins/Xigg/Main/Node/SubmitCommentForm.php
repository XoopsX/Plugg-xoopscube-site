<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Node_SubmitCommentForm extends Plugg_FormController
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

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_comment->applyForm($form);
        $this->_comment->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggSubmitCommentForm', array($context, $form, /*$isReply*/ false));
        $this->_comment->applyForm($form);
        $this->_comment->assignUser($context->user);
        $this->_comment->markNew();
        $this->_application->dispatchEvent('XiggSubmitComment', array($context, $this->_comment, /*$isReply*/ false));
        if ($this->_comment->commit()) {
            $context->response->setSuccess(sprintf($context->plugin->_('Reply to comment #%d posted successfully'), $this->_comment->getId()), array(
                'path' => '/' . $this->_application->node->getId(),
                'params' => array('comment_id' => $this->_comment->getId()),
                'fragment' => 'comment' . $this->_comment->getId()
            ));
            $this->_application->dispatchEvent('XiggSubmitCommentSuccess', array($context, $this->_application->node, $this->_comment, /*$isEdit*/ false));
            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggShowCommentForm', array($context, $form, /*$isReply*/ true));
        $context->response->setPageInfo($context->plugin->_('Submit comment'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_comment->toHTMLQuickForm();
        $form->removeElements(array('Node', 'body_html', 'allow_edit'));
        $form->setDefaults(array(
            'title' => 'Re: ' . trim($this->_application->node->title),
            'body' => "\n\n" . strtr("\n" . $this->_application->node->body, array("\n>" => "\n>>", "\n" => "\n> "))
        ));
        return $form;
    }
}