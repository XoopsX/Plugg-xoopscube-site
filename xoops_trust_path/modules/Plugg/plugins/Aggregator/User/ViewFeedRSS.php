<?php
class Plugg_Aggregator_User_ViewFeedRSS extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $items = $context->plugin->getModel()->Item
            ->criteria()
            ->hidden_is(0)
            ->fetchByFeed($this->_application->feed->getId(), 20, 0, 'item_published', 'DESC');

        $this->_application->setData(array(
            'items' => $items,
        ));

        $context->response->noLayout();
        $context->response->setContentStackLevel(1);
        $context->response->setCharset('UTF-8');
    }
}