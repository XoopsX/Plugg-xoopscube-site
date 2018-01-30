<?php
require_once 'Plugg/FormController.php';

class Plugg_User_Main_Identity_EditStatus extends Plugg_FormController
{
    private $_status;

    protected function _init(Sabai_Application_Context $context)
    {
        // Check permission if other user's profile
        if ($this->_application->identity->getId() != $context->user->getId()) {
            if (!$context->user->hasPermission('user status edit any')) {
                $context->response->setError($context->plugin->_('Permission denied'));
                return false;
            }
        }

        $model = $context->plugin->getModel();
        if (!$this->_status = $model->Status->fetchByUser($this->_application->identity->getId())->getNext()) {
            $this->_status = $model->create('Status');
            $this->_status->markNew();
            $this->_status->setVar('userid', $this->_application->identity->getId());
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_status->toHTMLQuickForm();
        $form->removeElementsExcept(array('text'));
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_status->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_status->applyForm($form);
        if ($this->_status->commit()) {
            $context->response->setSuccess(
                $context->plugin->_('User data updated successfully')
            );
            return true;;
        }
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        // Alter buttons if ajax
        if ($context->request->isAjax()) {
            $text = $form->getElement('text');
            $text->setCols(15);
            $text->setRows(5);
            $cancel_link = sprintf(
                '<a href="%s" onclick="%s">%s</a>',
                $this->_application->createUrl(),
                "jQuery('#plugg-user-statusform').hide(); jQuery('#plugg-user-status').show(); return false;",
                $context->plugin->_('Cancel')
            );
            $form->addSubmitButtons(array($this->_submitElementName => $context->plugin->_('Update')), $cancel_link);
            $form->setElementLabel('text', null);
            $form->hideElementsExcept(array('text'));
        }

        $context->response->setPageInfo($context->plugin->_('Edit status message'));
    }
}