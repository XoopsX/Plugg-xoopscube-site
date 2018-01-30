<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_Xigg_Admin extends Plugg_PluginAdmin
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
            'node/:node_id' => array(
                'controller'   => 'Node',
                'requirements' => array(
                    ':node_id' => '\d+'
                ),
                'access_callback' => '_onAccess',
            ),
            'tag' => array(
                'controller' => 'Tag',
                'tab' => true,
                'tab_ajax' => true,
                'title' => $context->plugin->_('Tags'),
            ),
            'category' => array(
                'controller' => 'Category',
                'tab' => true,
                'tab_ajax' => true,
                'title' => $context->plugin->_('Categories'),
            ),
        );
    }

    protected function _getDefaultTabTitle($context)
    {
        return $context->plugin->_('Articles');
    }

    protected function _onAccess($context, $controller)
    {
        if (!$node = $this->getRequestedEntity($context, 'Node', 'node_id')) {
            return false;
        }

        $this->_application->setData(array(
            'node_id' => $node->getId(),
            'node' => $node
        ));

        return true;
    }
}