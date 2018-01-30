<?php
class Plugg_Cron_Admin_RunCron extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm();
        $form->addSubmitButtons($context->plugin->_('Run cron now'));
        $form->useToken(get_class($this));
        if ($form->validate()) {
            $logs = array();
            $this->_application->cron($this->_application->getConfig('cronKey'), $logs);
            $context->response->setSuccess($context->plugin->_('Cron has run successfully'));
            return;
        }
        $this->_application->setData(array(
            'form' => $form
        ));
    }
}