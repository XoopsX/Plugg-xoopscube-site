<?php
class Plugg_Page_Main_ShowPageBySlug extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$slug = $context->request->getAsStr('page_slug')) {
            $context->response->setError($context->plugin->_('Invalid request'));

            return;
        }

        // Remove the trailing .html if any
        $slug = basename($slug, '.html');

        $page = $context->plugin->getModel()->Page
            ->criteria()
            ->slug_is($slug)
            ->fetch(1, 'page_created', 'DESC')
            ->getFirst();
        if (!$page) {
            $context->response->setError($context->plugin->_('Invalid request'));

            return;
        }

        $page->cache();
        $this->forward($page->getId(), $context);
    }
}