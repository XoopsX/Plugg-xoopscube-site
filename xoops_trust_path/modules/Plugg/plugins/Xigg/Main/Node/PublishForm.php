<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Node_PublishForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        $this->_confirmable = false;

        if (!$context->user->hasPermission('xigg publish any')) {
            if (!$this->_application->node->isOwnedBy($context->user) || !$context->user->hasPermission('xigg publish own')) {
                $context->response->setError($context->plugin->_('Permission denied'), array('path' => '/' . $this->_application->node->getId()));
                return false;
            }
        }

        return true;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->node->applyForm($form);
        $this->_application->node->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->node->publish();
        if ($this->_application->node->commit()) {
            $context->response->setSuccess($context->plugin->_('News article published successfully'), array('path' => '/' . $this->_application->node->getId()));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggShowNodeForm', array($context, $form, /*$isEdit*/ true));
        $context->response->setPageInfo($context->plugin->_('Publish article'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_application->node->toHTMLQuickForm();
        $form->removeElementsAll();
        return $form;
    }
}