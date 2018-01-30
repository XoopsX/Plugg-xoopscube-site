<?php
class Plugg_Aggregator_Admin_Feed_Feed extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Details', 'Plugg_Aggregator_Admin_Feed_Feed_', dirname(__FILE__) . '/Feed');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $context->response->setPageInfo(
            $this->_application->feed->title,
            array('path' => '/' . $this->_application->feed->getId()),
            true
        );
        return array(
            'edit' => array(
                'controller' => 'Edit',
            ),
        );
    }
}