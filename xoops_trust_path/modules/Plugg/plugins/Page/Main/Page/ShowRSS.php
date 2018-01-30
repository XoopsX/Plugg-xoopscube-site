<?php
class Plugg_Page_Main_Page_ShowRSS extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $context->response->noLayout();
        $context->response->setContentStackLevel(1);
        $context->response->setCharset('UTF-8');
        $page_id = $this->_application->page ->getId();
        $this->_application->setData(array(
            'children' => $context->plugin->getModel()->Page
                            ->fetchByParent($page_id, 10, 0, 'page_published', 'DESC')
        ));
    }
}