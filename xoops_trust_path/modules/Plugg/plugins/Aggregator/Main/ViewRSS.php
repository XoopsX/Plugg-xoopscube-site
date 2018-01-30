<?php
class Plugg_Aggregator_Main_ViewRSS extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();

        $items = $model->Item
            ->criteria()
            ->hidden_is(0)
            ->fetch(20, 0, 'item_published', 'DESC');

        $this->_application->setData(array(
            'items' => $items
        ));

        $context->response->noLayout();
        $context->response->setContentStackLevel(1);
        $context->response->setCharset('UTF-8');
    }
}