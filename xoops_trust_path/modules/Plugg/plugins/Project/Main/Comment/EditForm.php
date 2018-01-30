<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Comment_EditForm extends Plugg_FormController
{
    private $_comment;

    protected function _init(Sabai_Application_Context $context)
    {
        if (!$this->_comment = $this->getRequestedComment($context)) {
            return false;
        }
        if (!$this->_comment->Project->isReadable($context->user)) {
            return false;
        }
        if (!$context->user->hasPermission('project comment edit any')) {
            // is the user poster and allowed to edit?
            if (!$this->_comment->isOwnedBy($context->user) ||
                !$context->user->hasPermission('project comment edit posted')
            ) {
                // not the poster, then is the user a developer of the project?
                if (!$this->_comment->Project->isDeveloper($context->user)) {
                    $context->response->setError($context->plugin->_('Permission denied'), array(
                        'path' => '/' . $this->_comment->Project->getId()
                    ));
                    return false;
                }
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_comment->toHTMLQuickForm('', '', 'post');
        if (!$context->user->hasPermission('project comment allow edit')) {
            $form->removeElement('allow_edit');
        }
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_comment->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_comment->applyForm($form);
        if ($this->_comment->commit()) {
            // reload project
            if (!$this->_comment->Project->reload()->updateCommentRating()) {

            }
            $context->response->setSuccess($context->plugin->_('Comment posted successfully'), array(
                'path' => '/' . $this->_comment->Project->getId(),
                'params' => array(
                    'view' => 'comments',
                    'comment_id' => $this->_comment->getId()
                ),
                'fragment' => 'comment' . $this->_comment->getId()
            ));
            $this->_application->dispatchEvent('ProjectSubmitCommentSuccess',
                array($context, $this->_comment->Project, $this->_comment, /*$isEdit*/ true));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Edit comment'));
    }
}