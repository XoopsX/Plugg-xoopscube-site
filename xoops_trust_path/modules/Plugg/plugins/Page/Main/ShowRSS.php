<?php
class Plugg_Page_Main_ShowRSS extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $context->response->noLayout();
        $context->response->setContentStackLevel(1);
        $context->response->setCharset('UTF-8');

        $pages = $context->plugin->getModel()->Page->fetch(10, 0, 'page_published', 'DESC');
        $this->_application->setData(array(
            'pages' => $pages,
        ));
    }
}