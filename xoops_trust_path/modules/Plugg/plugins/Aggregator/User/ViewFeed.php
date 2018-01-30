<?php
class Plugg_Aggregator_User_ViewFeed extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $items_sortby_allowed = array(
            'published,DESC' => $context->plugin->_('Newest first'),
            'published,ASC' => $context->plugin->_('Oldest first'),
        );
        $items_sortby = $context->request->getAsStr('sortby', 'published,DESC', array_keys($items_sortby_allowed));

        $sortby = explode(',', $items_sortby);
        $pages = $context->plugin->getModel()->Item
            ->criteria()
            ->hidden_is(0)
            ->paginateByFeed($this->_application->feed->getId(), 20, 'item_' . $sortby[0], $sortby[1]);
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));

        $this->_application->setData(array(
            'items' => $page->getElements()->with('Feed'),
            'pages' => $pages,
            'page' => $page,
            'sortby' => $items_sortby,
            'sortby_allowed' => $items_sortby_allowed,
        ));
        $context->response->setPageInfo(
            $this->_application->feed->title,
            array('path' => '/' . $this->_application->feed->getId())
        );
    }
}