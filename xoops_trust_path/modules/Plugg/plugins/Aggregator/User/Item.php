<?php
class Plugg_Aggregator_User_Item extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', __CLASS__ . '_', dirname(__FILE__) . '/Item');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $context->response->setPageInfo(
            $this->_application->feed->title,
            array('path' => '/' . $this->_application->feed->getId()),
            true
        );
        $context->response->setPageInfo(
            $this->_application->feed_item->title,
            array('path' => '/item/' . $this->_application->feed_item->getId()),
            true
        );
        return array(
            'delete' => array(
                'controller' => 'Delete',
            ),
            'edit' => array(
                'controller' => 'Edit',
            ),
        );
    }
}