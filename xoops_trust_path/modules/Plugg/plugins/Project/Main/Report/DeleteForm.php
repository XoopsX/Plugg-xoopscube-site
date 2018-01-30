<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Report_DeleteForm extends Plugg_FormController
{
    var $_report;

    protected function _init(Sabai_Application_Context $context)
    {
        if (!$this->_report = $this->getRequestedReport($context)) {
            return false;
        }

        if ((!$release = $this->_report->Release) ||
            !$release->Project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project report edit')) {
            // only users with a role higher than the contributor role are allowed
            if ((!$developer_role = $release->Project->isDeveloper($context->user)) ||
                $developer_role < Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR
            ) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/release/' . $release->getId()
                ));
                return false;
            }
        }

        $this->_confirmable = false;
        $this->_submitPhrase = $context->plugin->_('Delete');

        return true;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_report->markRemoved();

        if ($this->_report->commit()) {
            $context->response->setSuccess($context->plugin->_('Report deleted successfully'), array(
                'path' => '/release/' . $this->_report->Release->getId(),
            ));
            $this->_application->dispatchEvent('ProjectDeleteReportSuccess',
                array($context, $this->_report->Release->Project, $this->_report->Release, $this->_report));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Delete report'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_report->toHTMLQuickForm();
        $form->removeElementsAll();
        $version = $this->_report->Release->Project->name . ' ' . $this->_report->Release->getVersionStr();
        $form->addElement('static', '', $context->plugin->_('Version'), h($version));
        $form->addElement('static', '', $context->plugin->_('Report ID'), $this->_report->getId());
        return $form;
    }
}