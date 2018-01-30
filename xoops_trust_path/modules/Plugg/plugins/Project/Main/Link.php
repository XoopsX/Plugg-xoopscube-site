<?php
class Plugg_Project_Main_Link extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', 'Plugg_Project_Main_Link_', dirname(__FILE__) . '/Link');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        $authenticated = $context->user->isAuthenticated();
        return array(
            'vote' => array(
                'controller' => 'Vote',
            ),
            'voteform' => array(
                'controller' => 'VoteForm',
            ),
            'edit' => array(
                'controller' => 'EditForm',
                'access' => $authenticated
            ),
            'delete' => array(
                'controller' => 'DeleteForm',
                'access' => $authenticated
            ),
            'reportform' => array(
                'controller' => 'ShowReportForm',
                'access' => $authenticated
            ),
            'report' => array(
                'controller' => 'SubmitReportForm',
                'access' => $authenticated
            ),
        );
    }

    function getRequestedLink(Sabai_Application_Context $context, $noCache = false)
    {
        $ret = $this->getRequestedEntity($context, 'Link', 'link_id', $noCache);
        $context->response->setPageInfo($ret->Project->name, array('path' => '/' . $ret->Project->getId()));
        $context->response->setPageInfo($ret->title, array('path' => '/link/' . $ret->getId()));
        return $ret;
    }
}