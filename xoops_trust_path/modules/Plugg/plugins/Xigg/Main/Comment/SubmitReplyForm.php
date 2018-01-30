<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Comment_SubmitReplyForm extends Plugg_FormController
{
    private $_reply;

    protected function _init(Sabai_Application_Context $context)
    {
        if (!$this->_application->comment->Node->allow_comments ||
            !$this->_application->comment->Node->isReadable($context->user)
        ) {
            $context->response->setError($context->plugin->_('Comments not allowed for this news article'), array(
                'path' => '/' . $this->_application->comment->Node->getId(),
                'params' => array('comment_id' => $this->_application->comment->getId()),
                'fragment' => 'comment' . $this->_application->comment->getId()
            ));
            return false;
        }
        $this->_reply = $this->_application->comment->Node->createComment();

        return true;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_reply->applyForm($form);
        $this->_reply->setVar('parent', $this->_application->comment->getId());
        $this->_reply->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggSubmitCommentForm', array($context, $form, /*$isReply*/ true));
        $this->_reply->applyForm($form);
        $this->_reply->setVar('parent', $this->_application->comment->getId());
        $this->_reply->assignUser($context->user);
        $this->_reply->markNew();
        $this->_application->dispatchEvent('XiggSubmitComment', array($context, $this->_reply, /*$isReply*/ true));
        if ($this->_reply->commit()) {
            $context->response->setSuccess(sprintf($context->plugin->_('Reply to comment #%d posted successfully'), $this->_application->comment->getId()), array(
                'path' => '/' . $this->_application->comment->Node->getId(),
                'params' => array('comment_id' => $this->_reply->getId()),
                'fragment' => 'comment' . $this->_reply->getId()
            ));
            $this->_application->dispatchEvent('XiggSubmitCommentSuccess', array($context, $this->_application->comment->Node, $this->_reply, /*$isEdit*/ false));
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
        $form = $this->_reply->toHTMLQuickForm();
        $form->removeElements(array('Node', 'body_html', 'allow_edit'));
        $comment_title = trim($this->_application->comment->title);
        $form->setDefaults(array(
            'title' => !preg_match('/^Re:/i', $comment_title) ? 'Re: ' . $comment_title : $comment_title,
            'body' => "\n\n" . strtr("\n" . $this->_application->comment->body, array("\n>" => "\n>>", "\n" => "\n> "))
        ));
        return $form;
    }
}