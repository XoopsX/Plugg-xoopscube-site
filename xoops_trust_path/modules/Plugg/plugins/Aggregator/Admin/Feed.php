<?php
class Plugg_Aggregator_Admin_Feed extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('List', 'Plugg_Aggregator_Admin_Feed_', dirname(__FILE__) . '/Feed');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $default_base = '/' . $context->plugin->getName() . '/feed';
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));

        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            'update_items' => array(
                'controller' => 'UpdateItems',
                'callback' => true
            ),
            'add' => array(
                'controller' => 'Add',
            ),
            ':feed_id' => array(
                'controller' => 'Feed',
                'requirements' => array(
                    ':feed_id' => '\d+'
                ),
                'access_callback' => '_onAccess',
            )
        );
    }

    protected function _onAccess($context, $controller)
    {
        if (!$this->_application->feed = $this->getRequestedEntity($context, 'Feed', 'feed_id')) {
            return false;
        }

        return true;
    }
}