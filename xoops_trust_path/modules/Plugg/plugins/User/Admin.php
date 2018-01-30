<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_User_Admin extends Plugg_PluginAdmin
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Admin');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            'autologin' => array(
                'controller' => 'Autologin',
                'title' => $context->plugin->_('Autologins'),
                'tab' => true,
                'tab_ajax' => true,
            ),
            'queue' => array(
                'controller' => 'Queue',
                'title' => $context->plugin->_('Queues'),
                'tab' => true,
                'tab_ajax' => true,
            ),
            'menu' => array(
                'controller' => 'Menu',
                'title' => $context->plugin->_('Menu'),
                'tab' => true,
                'tab_ajax' => true,
            ),
            'widget' => array(
                'controller' => 'Widget',
                'title' => $context->plugin->_('Widgets'),
                'tab' => true,
                'tab_ajax' => false
            ),
            'field' => array(
                'controller' => 'Field',
                'title' => $context->plugin->_('Fields'),
                'tab' => true,
                'tab_ajax' => true,
            ),
            'auth' => array(
                'controller' => 'Auth',
                'title' => $context->plugin->_('Authentications'),
                'tab' => true,
                'tab_ajax' => true,
            ),
            'role' => array(
                'controller' => 'Role',
                'title' => $context->plugin->_('Roles'),
                'tab' => true,
                'tab_ajax' => true,
            ),
        );
    }

    protected function _getDefaultTabTitle($context)
    {
        return $context->plugin->_('Users');
    }
}