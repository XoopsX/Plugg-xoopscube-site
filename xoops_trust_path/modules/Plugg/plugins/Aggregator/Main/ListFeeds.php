<?php
class Plugg_Aggregator_Main_ListFeeds extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $sortby_allowed = array(
            'created,DESC' => $context->plugin->_('Newest first'),
            'created,ASC' => $context->plugin->_('Oldest first'),
        );
        $sortby = $context->request->getAsStr('sortby', 'created,DESC', array_keys($sortby_allowed));
        $sortby_parts = explode(',', $sortby);
        $pages = $context->plugin->getModel()->Feed
            ->criteria()
            ->status_is(Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED)
            ->paginate(10, 'feed_' . $sortby_parts[0], $sortby_parts[1]);
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));

        $this->_application->setData(array(
            'feeds' => $page->getElements()->with('User')->with('LastItem'),
            'pages' => $pages,
            'page' => $page,
            'sortby' => $sortby,
            'sortby_allowed' => $sortby_allowed,
        ));
    }
}