<?php
class Plugg_Project_Main_Report extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', 'Plugg_Project_Main_Report_', dirname(__FILE__) . '/Report');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        $authenticated = $context->user->isAuthenticated();
        return array(
            'edit' => array(
                'controller' => 'EditForm',
                'access' => $authenticated
            ),
            'delete' => array(
                'controller' => 'DeleteForm',
                'access' => $authenticated
            ),
        );
    }

    function getRequestedReport(Sabai_Application_Context $context, $noCache = false)
    {
        $ret = $this->getRequestedEntity($context, 'Report', 'report_id', $noCache);
        $context->response->setPageInfo($ret->Release->Project->name, array('path' => '/' . $ret->Release->Project->getId()));
        $context->response->setPageInfo($ret->Release->getVersionStr(), array('path' => '/release/' . $ret->Release->getId()));
        $context->response->setPageInfo(sprintf($context->plugin->_('Report #%d'), $ret->getId()), array('path' => '/report/' . $ret->getId()));
        return $ret;
    }
}