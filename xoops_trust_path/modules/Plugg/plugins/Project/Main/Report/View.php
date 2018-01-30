<?php
class Plugg_Project_Main_Report_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$report = $this->getRequestedReport($context))) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $report_id = $report->getId();
        header('Location: ' . $this->_application->createUrl(array(
            'path' => '/release/' . $report->getVar('release_id'),
            'params' => array('report_id' => $report_id),
            'fragment' => 'report' . $report_id,
            'separator' => '&'
        )));
        exit;
    }
}