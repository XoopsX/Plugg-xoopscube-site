<?php
require_once 'Plugg/PluginMain.php';

class Plugg_Page_Main extends Plugg_PluginMain
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Main', 'ShowTOC');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'rss' => array(
                'controller' => 'ShowRSS'
            ),
            'add' => array(
                'controller' => 'AddPageForm',
                'access_callback' => '_onAccess',
            ),
            ':page_slug' => array(
                'controller' => 'ShowPageBySlug',
                'requirements' => array(':page_slug' => '[a-zA-Z0-9~\s\.:_\-\/]+\.html'),
                'callback' => true
            ),
            ':page_id' => array(
                'controller' => 'Page',
                'requirements' => array(':page_id' => '\d+'),
                'access_callback' => '_onAccess',
            ),
        );
    }

    protected function _onAccess($context, $controller)
    {
        switch ($controller) {
            case 'Page':
                if (!$page = $this->getRequestedEntity($context, 'Page', 'page_id')) {
                    return false;
                }
                $this->_application->page = $page;
                break;

            case 'AddPageForm':
                if ($context->user->isAuthenticated() ||
                    !$context->user->hasPermission('page add')
                ) {
                    return false;
                }
                break;
        }

        return true;
    }
}