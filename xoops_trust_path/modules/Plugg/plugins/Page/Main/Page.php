<?php
class Plugg_Page_Main_Page extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('Show', __CLASS__ . '_', dirname(__FILE__) . '/Page');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($this->_application->page->title);

        return array(
            'edit' => array(
                'controller' => 'EditForm',
                'access_callback' => '_onAccess'
            ),
            'delete' => array(
                'controller' => 'DeleteForm',
                'access_callback' => '_onAccess'
            ),
            'move' => array(
                'controller' => 'MoveForm',
                'access_callback' => '_onAccess'
            ),
            'rss' => array(
                'controller' => 'ShowRSS',
            ),
        );
    }

    protected function _onAccess($context, $controller)
    {
        if (!$context->user->isAuthenticated()) {
            return false;
        }

        if ($controller == 'MovePageForm' &&
            !$context->user->hasPermission('page move')
        ) {
            return false;
        }

        return true;
    }
}