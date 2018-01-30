<?php
class Plugg_Aggregator_User_ListFeeds extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $pages = $context->plugin->getModel()->Feed
            ->criteria()
            ->status_is(Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED)
            ->paginateByUser($this->_application->identity->getId(), 10, 'feed_name', 'ASC');
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));

        $this->_application->setData(array(
            'feeds' => $page->getElements()->with('User')->with('LastItem'),
            'pages' => $pages,
            'page' => $page,
        ));
    }
}