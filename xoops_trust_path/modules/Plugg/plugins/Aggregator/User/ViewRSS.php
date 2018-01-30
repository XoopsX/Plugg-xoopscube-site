<?php
class Plugg_Aggregator_User_ViewRSS extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();

        $feeds = $model->Feed
            ->criteria()
            ->status_is(Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED)
            ->fetchByUser($this->_application->identity->getId());
        if ($feeds->count() == 0) {
            return;
        }

        $items = $model->Item
            ->criteria()
            ->hidden_is(0)
            ->feedId_in($feeds->getAllIds())
            ->fetch(20, 0, 'item_published', 'DESC');

        $this->_application->setData(array(
            'items' => $items
        ));

        $context->response->noLayout();
        $context->response->setContentStackLevel(1);
        $context->response->setCharset('UTF-8');
    }
}