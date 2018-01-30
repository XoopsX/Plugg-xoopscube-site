<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Trackback_EditForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        if (!$context->user->hasPermission('xigg trackback edit')) {
            $context->response->setError($context->plugin->_('Permission denied'), array('path' => '/trackback/' . $trackback_id));
            return false;
        }

        return true;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->trackback->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggSubmitEditTrackbackForm', array($context, $form));
        $this->_application->trackback->applyForm($form);
        $this->_application->dispatchEvent('XiggEditTrackback', array($context, $this->_application->trackback));
        if ($this->_application->trackback->commit()) {
            $context->response->setSuccess($context->plugin->_('Trackback updated successfully'), array(
                'path' => '/' . $this->_application->trackback->Node->getId(),
                'params' => array('trackback_id' => $this->_application->trackback->getId()),
                'fragment' => 'trackback' . $this->_application->trackback->getId()
            ));
            $this->_application->dispatchEvent('XiggEditTrackbackSuccess', array($context, $this->_application->trackback));
            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggShowEditTrackbackForm', array($context, $form));
        $context->response->setPageInfo($context->plugin->_('Edit trackback'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        return $this->_application->trackback->toHTMLQuickForm();
    }
}