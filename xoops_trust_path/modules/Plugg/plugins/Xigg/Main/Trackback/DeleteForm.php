<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Trackback_DeleteForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        $this->_confirmable = false;

        if (!$context->user->hasPermission('xigg trackback delete')) {
            $context->response->setError($context->plugin->_('Permission denied'), array('path' => '/trackback/' . $trackback_id));
            return false;
        }

        return true;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->trackback->markRemoved();
        if ($this->_application->trackback->commit()) {
            $context->response->setSuccess($context->plugin->_('Trackback updated successfully'), array(
                'path' => '/' . $this->_application->trackback->Node->getId(),
            ));
            $this->_application->dispatchEvent('XiggDeleteTrackbackSuccess', array($context, $this->_application->trackback));
            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggShowDeleteTrackbackForm', array($context, $form));
        $context->response->setPageInfo($context->plugin->_('Delete trackback'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_application->trackback->toHTMLQuickForm();
        $form->removeElementsAll();
        return $form;
    }
}