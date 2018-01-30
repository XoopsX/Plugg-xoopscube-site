<?php
class Plugg_Project_Main_Developer extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', 'Plugg_Project_Main_Developer_', dirname(__FILE__) . '/Developer');
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
        );
    }

    function getRequestedDeveloper(Sabai_Application_Context $context, $noCache = false)
    {
        $ret = $this->getRequestedEntity($context, 'Developer', 'developer_id', $noCache);
        $context->response->setPageInfo($ret->Project->name, array('path' => '/' . $ret->Project->getId()));
        $context->response->setPageInfo(sprintf($context->plugin->_('Developer #%d'), $ret->getId()), array('path' => '/developer/' . $ret->getId()));
        return $ret;
    }
}