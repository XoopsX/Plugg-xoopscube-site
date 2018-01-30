<?php
class Plugg_Project_Main_Release extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', 'Plugg_Project_Main_Release_', dirname(__FILE__) . '/Release');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        $authenticated = $context->user->isAuthenticated();
        return array(
            'approve' => array(
                'controller' => 'ApproveForm',
                'access' => $authenticated
            ),
            'edit' => array(
                'controller' => 'EditForm',
                'access' => $authenticated
            ),
            'delete' => array(
                'controller' => 'DeleteForm',
                'access' => $authenticated
            ),
            'reports/rss' => array(
                'controller' => 'ViewReportsRSS',
            ),
            'reports' => array(
                'controller' => 'ViewReports',
            ),
            'report' => array(
                'controller' => 'SubmitReportForm',
                'access' => $authenticated
            ),
            'reportform' => array(
                'controller' => 'ShowReportForm',
                'access' => $authenticated
            ),
            'download' => array(
                 'controller' => 'Download',
            ),
        );
    }

    function getRequestedRelease(Sabai_Application_Context $context, $noCache = false)
    {
        $ret = $this->getRequestedEntity($context, 'Release', 'release_id', $noCache);
        $context->response->setPageInfo($ret->Project->name, array('path' => '/' . $ret->Project->getId()));
        $context->response->setPageInfo($ret->getVersionStr(), array('path' => '/release/' . $ret->getId()));
        return $ret;
    }
}