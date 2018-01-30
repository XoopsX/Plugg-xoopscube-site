<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Comment_DeleteForm extends Plugg_FormController
{
    var $_comment;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_comment = $this->getRequestedComment($context)) ||
            !$this->_comment->Project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project comment delete any')) {
            // is the user poster and allowed to delete?
            if (!$comment->isOwnedBy($context->user) ||
                !$context->user->hasPermission('project comment delete posted')
            ) {
                if (!$this->_comment->Project->isDeveloper($context->user)) {
                    $context->response->setError($context->plugin->_('Permission denied'), array(
                        'path' => '/' . $this->_comment->Project->getId()
                    ));
                    return false;
                }
            }
        }

        $this->_confirmable = false;
        $this->_submitPhrase = $context->plugin->_('Delete');

        return true;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_comment->markRemoved();

        if ($this->_comment->commit()) {
            // reload project
            $this->_comment->clearObject('Project');
            if (!$this->_comment->Project->updateCommentRating()) {

            }
            $context->response->setSuccess($context->plugin->_('Comment deleted successfully'), array(
                'path' => '/' . $this->_comment->Project->getId(),
                'params' => array('view' => 'comments')
            ));
            $this->_application->dispatchEvent('ProjectDeleteCommentSuccess',
                array($context, $this->_comment->Project, $this->_comment));

            return true;
        }

        return false;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_comment->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Title'), h($this->_comment->title));
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Delete comment'));
    }
}