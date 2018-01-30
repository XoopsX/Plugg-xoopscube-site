<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Release_SubmitReportForm extends Plugg_FormController
{
    private $_release;
    private $_report;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_release = $this->getRequestedRelease($context)) ||
            !$this->_release->get('allow_reports') ||
            !$this->_release->Project->isReadable($context->user)
        ) {
            return false;
        }
        if (!$this->_release->isApproved() &&
            !$context->user->hasPermission('project release approve')
        ) {
            // only developers are allowed to view pending releases
            if (!$this->_release->Project->isDeveloper($context->user)) {
                $context->response->setError($context->plugin->_('Invalid request'), array(
                    'path' => '/' . $this->_release->Project->getId()
                ));
                return false;
            }
        }

        $this->_report = $this->_release->createReport();

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
        $this->_report->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_report->applyForm($form);
        $this->_report->assignUser($context->user);
        $this->_report->set('ip', getip());
        $this->_report->markNew();
        if ($this->_report->commit()) {
            $context->response->setSuccess($context->plugin->_('Report submitted successfully'), array(
                'path' => '/release/' . $this->_release->getId(),
                'params' => array('report_id' => $this->_report->getId()),
                'fragment' => 'report' . $this->_report->getId()
            ));
            $this->_application->dispatchEvent('ProjectSubmitReportSuccess',
                array($context, $this->_release->Project, $this->_release, $this->_report, /*$isEdit*/ false));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Submit report'));
    }
}