<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_SubmitCommentForm extends Plugg_FormController
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

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_comment->toHTMLQuickForm('', '', 'post');
        $form->removeElements(array('allow_edit'));
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_comment->applyForm($form);
        $this->_comment->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_comment->applyForm($form);
        $this->_comment->assignUser($context->user);
        $this->_comment->set('ip', getip());
        if (!$this->_project->isApproved()) {
            $this->_comment->setPending();
        } else {
            $this->_comment->setApproved();
        }
        $this->_comment->markNew();
        if ($this->_comment->commit()) {
            // reload project
            if (!$this->_project->reload()->updateCommentRating()) {

            }
            $context->response->setSuccess($context->plugin->_('Comment posted successfully'), array(
                'path' => '/' . $this->_project->getId(),
                'params' => array(
                    'view' => 'comments',
                    'comment_id' => $this->_comment->getId()
                ),
                'fragment' => 'comment' . $this->_comment->getId()
            ));
            $this->_application->dispatchEvent('ProjectSubmitCommentSuccess', array($context, $this->_project, $this->_comment, /*$isEdit*/ false));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add comment'));
    }
}