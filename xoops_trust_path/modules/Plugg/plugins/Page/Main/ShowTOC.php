<?php
class Plugg_Page_Main_ShowTOC extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $page_pages = $context->plugin->getModel()->Page
            ->criteria()
            ->parent_is('NULL')
            ->paginate(50, 'page_created', 'DESC');
        $page_page = $page_pages->getValidPage($context->request->getAsInt('page', 1, null, 0));

        $this->_application->setData(array(
            'page_pages' => $page_pages,
            'page_page' => $page_page,
            'pages' => $page_page->getElements()->with('Children', 'DescendantsCount'),
            'show_admin' => $context->plugin->getParam('lock') ? false : true,
        ));
    }
}