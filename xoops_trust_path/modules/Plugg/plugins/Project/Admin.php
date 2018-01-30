<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_Project_Admin extends Plugg_PluginAdmin
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Admin');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'category' => array(
                'controller' => 'Category',
                'tab' => true,
                'tab_ajax' => true,
                'title' => $context->plugin->_('Categories')
            ),
            'app' => array(
                'controller' => 'Project',
                'tab' => true,
                'tab_ajax' => true,
                'title' => $context->plugin->_('Projects')
            ),
        );
    }
}