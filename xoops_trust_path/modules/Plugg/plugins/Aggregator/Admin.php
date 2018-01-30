<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_Aggregator_Admin extends Plugg_PluginAdmin
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Admin');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true,
            ),
            ':item_id' => array(
                'controller'   => 'Item',
                'requirements' => array(
                    ':node_id' => '\d+'
                ),
                'access_callback' => '_onAccess',
                'parent_tab' => 'feed'
            ),
            'feed' => array(
                'controller' => 'Feed',
                'tab' => true,
                'tab_ajax' => true,
                'title' => $context->plugin->_('Feeds'),
            ),
        );
    }

    protected function _getDefaultTabTitle($context)
    {
        return $context->plugin->_('Articles');
    }

    protected function _onAccess($context, $controller)
    {
        if (!$item = $this->getRequestedEntity($context, 'Item', 'item_id')) {
            return false;
        }

        $this->_application->setData(array(
            'feed_item_id' => $item->getId(),
            'feed_item' => $item
        ));

        return true;
    }
}