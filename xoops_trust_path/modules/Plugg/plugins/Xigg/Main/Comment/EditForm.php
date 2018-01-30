<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Comment_EditForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        if (!$context->user->hasPermission('xigg comment edit any')) {
            if (!$this->_application->comment->allow_edit) {
                $context->response->setError($context->plugin->_('This comment has been frozen by the administration'), array('path' => '/comment/' . $comment_id));
                return false;
            }
            if (!$this->_application->comment->isOwnedBy($context->user) ||
                !$context->user->hasPermission('xigg comment edit own')
            ) {
                $context->response->setError($context->plugin->_('Permission denied'), array('path' => '/comment/' . $comment_id));
                return false;
            }
            if (time() > $this->_application->comment->getTimeCreated() + $context->plugin->getParam('userCommentEditTime')) {
                $context->response->setError($context->plugin->_('Time allowed to edit your comment has expired'), array(
                    'path' => '/' . $this->_application->comment->getVar('node_id'),
                    'params' => array('comment_id' => $comment_id),
                    'fragment' => 'comment' . $comment_id
                ));
                return false;
            }
        }

        return true;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->comment->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggSubmitCommentForm', array($context, $form, /*$isReply*/ false));
        $this->_application->comment->applyForm($form);
        $this->_application->dispatchEvent('XiggSubmitComment', array($context, $this->_application->comment, /*$isReply*/ false));
        if ($this->_application->comment->commit()) {
            $context->response->setSuccess($context->plugin->_('Comment updated successfully'), array(
                'path' => '/' . $this->_application->comment->Node->getId(),
                'params' => array('comment_id' => $this->_application->comment->getId()),
                'fragment' => 'comment' . $this->_application->comment->getId()
            ));
            $this->_application->dispatchEvent('XiggSubmitCommentSuccess', array($context, $this->_application->comment->Node, $this->_application->comment, /*$isEdit*/ true));
            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggShowCommentForm', array($context, $form, /*$isReply*/ true));
        $context->response->setPageInfo($context->plugin->_('Edit comment'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_application->comment->toHTMLQuickForm();
        $form->removeElements(array('Node', 'body_html'));
        if (!$context->user->hasPermission('xigg comment allow edit')) {
            $form->removeElements(array('allow_edit'));
        }

        return $form;
    }
}