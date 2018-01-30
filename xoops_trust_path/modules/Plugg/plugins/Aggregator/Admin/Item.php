<?php
class Plugg_Aggregator_Admin_Item extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Details', 'Plugg_Aggregator_Admin_Item_', dirname(__FILE__) . '/Item');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $context->response->setPageInfo(
            $this->_application->feed_item->Feed->title,
            array('path' => '/feed/' . $this->_application->feed_item->Feed->getId()),
            true
        );
        return array(
            'edit' => array(
                'controller' => 'Edit',
            ),
        );
    }
}