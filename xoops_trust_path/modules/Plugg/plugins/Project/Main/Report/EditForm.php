<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Report_EditForm extends Plugg_FormController
{
    private $_release;
    private $_report;

    protected function _init(Sabai_Application_Context $context)
    {
        if (!$this->_report = $this->getRequestedReport($context)) {
            return false;
        }

        if ((!$this->_release = $this->_report->Release) ||
            !$this->_release->Project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project report edit')) {
            // only users with a role higher than the contributor role are allowed
            if ((!$developer_role = $this->_release->Project->isDeveloper($context->user)) ||
                $developer_role < Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR
            ) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/release/' . $this->_release->getId()
                ));
                return false;
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_report->toHTMLQuickForm('', '', 'post', array(
            'elements' => $context->plugin->getReportFormElementDefinitions(),
        ));
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_report->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_report->applyForm($form);
        if ($this->_report->commit()) {
            $context->response->setSuccess($context->plugin->_('Report updated successfully'), array(
                'path' => '/release/' . $this->_release->getId(),
                'params' => array('report_id' => $this->_report->getId()),
                'fragment' => 'report' . $this->_report->getId()
            ));
            $this->_application->dispatchEvent('ProjectSubmitReportSuccess',
                array($context, $this->_release->Project, $this->_release, $this->_report, /*$isEdit*/ true));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Edit report'));
    }
}