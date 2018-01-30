<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Release_ShowReportForm extends Plugg_FormController
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
        $this->_submitable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_report->toHTMLQuickForm(
            '',
            $this->_application->createUrl(array(
                'path' => '/release/' . $this->_release->getId() . '/report')
            ),
            'post',
            array(
                'elements' => $context->plugin->getReportFormElementDefinitions(),
            )
        );
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Submit report'));
    }
}