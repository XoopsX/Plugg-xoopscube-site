<?php
class Plugg_Project_Main_Comment extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', 'Plugg_Project_Main_Comment_', dirname(__FILE__) . '/Comment');
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

    function getRequestedComment(Sabai_Application_Context $context, $noCache = false)
    {
        $ret = $this->getRequestedEntity($context, 'Comment', 'comment_id', $noCache);
        $context->response->setPageInfo($ret->Project->name, array('path' => '/' . $ret->Project->getId()));
        $context->response->setPageInfo($ret->title, array('path' => '/comment/' . $ret->getId()));
        return $ret;
    }
}