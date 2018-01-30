<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Comment_ShowReplyForm extends Plugg_FormController
{
    private $_reply;

    protected function _init(Sabai_Application_Context $context)
    {
        $node = $this->_application->comment->Node;
        if (!$node->allow_comments || !$node->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Comments not allowed for this news article'), array(
                'path' => '/' . $node->getId(),
                'params' => array('comment_id' => $this->_application->comment->getId()),
                'fragment' => 'comment' . $this->_application->comment->getId()
            ));
            return false;
        }
        $this->_reply = $node->createComment();

        return true;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggShowCommentForm', array($context, $form, /*$isReply*/ true));
        $context->response->setPageInfo($context->plugin->_('Submit comment'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_reply->toHTMLQuickForm('', $this->_application->createUrl(array(
            'path' => '/comment/' . $this->_application->comment->getId() . '/reply'))
        );
        $form->removeElements(array('Node', 'body_html', 'allow_edit'));
        $comment_title = trim($this->_application->comment->title);
        $form->setDefaults(array(
            'title' => !preg_match('/^Re:/i', $comment_title) ? 'Re: ' . $comment_title : $comment_title,
            'body' => "\n\n" . strtr("\n" . $this->_application->comment->body, array("\n>" => "\n>>", "\n" => "\n> "))
        ));
        return $form;
    }
}