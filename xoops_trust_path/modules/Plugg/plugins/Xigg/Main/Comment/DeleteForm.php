<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Comment_DeleteForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        $this->_confirmable = false;

        if (!$context->user->hasPermission('xigg comment delete any')) {
            if (!$this->_application->comment->isOwnedBy($context->user) ||
                !$context->user->hasPermission('xigg comment delete own')
            ) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/comment/' . $this->_application->comment->getId()
                ));
                return false;
            }
        }

        return true;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->comment->markRemoved();
        $return_url = array('path' => '/' . $this->_application->comment->getVar('node_id'));
        if ($context->plugin->getModel()->commit()) {
            $context->response->setSuccess(sprintf($context->plugin->_('Comment #%d deleted successfully'), $this->_application->comment->getId()), $return_url);
            $this->_application->dispatchEvent('XiggDeleteCommentSuccess', array($context, $this->_application->comment));
            return true;
        }

        $error = $context->plugin->_('Comment #%d could not be deleted. There was either an error while commit or the comment has one or more child comments.');
        $context->response->setError(sprintf($error, $this->_application->comment->getId()), $return_url);

        return false;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_application->comment->toHTMLQuickForm();
        $form->removeElementsAll();
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Delete comment'));
    }
}