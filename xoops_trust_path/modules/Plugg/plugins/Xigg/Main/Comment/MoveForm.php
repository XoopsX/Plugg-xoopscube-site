<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Comment_MoveForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        $this->_confirmable = false;

        if (!$context->user->hasPermission('xigg comment move any')) {
            if (!$this->_application->comment->isOwnedBy($context->user) ||
                !$context->user->hasPermission('xigg comment move own')
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
        $this->_application->comment->setVar('parent', $form->getSubmitValue('move_to'));
        if ($this->_application->comment->commit()) {
            $context->response->setSuccess($context->plugin->_('Comment moved successfully'), array(
                'path' => '/comment/' . $this->_application->comment->getId(),
                'fragment' => 'comment' . $this->_application->comment->getId()
            ));

            return true;
        }

        return false;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm();
        $form->addElement('text', 'move_to', array($context->plugin->_('New parent comment ID'), $context->plugin->_('Enter the new parent comment ID, or 0 to move the comment to the top level.')), array('size' => 5, 'maxlength' => 10));
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Move comment'));
    }
}