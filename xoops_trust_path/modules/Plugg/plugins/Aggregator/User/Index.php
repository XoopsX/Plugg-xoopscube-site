<?php
class Plugg_Aggregator_User_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $items_sortby_allowed = array(
            'published,DESC' => $context->plugin->_('Newest first'),
            'published,ASC' => $context->plugin->_('Oldest first'),
        );
        $items_sortby = $context->request->getAsStr('sortby', 'published,DESC', array_keys($items_sortby_allowed));

        $model = $context->plugin->getModel();

        $feeds = $model->Feed
            ->criteria()
            ->status_is(Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED)
            ->fetchByUser($this->_application->identity->getId());
        if ($feeds->count() == 0) {
            $this->_application->setData(array(
                'items_sortby' => $items_sortby,
                'items_sortby_allowed' => $items_sortby_allowed,
            ));
            return;
        }

        $sortby = explode(',', $items_sortby);
        $pages = $model->Item
            ->criteria()
            ->hidden_is(0)
            ->feedId_in($feeds->getAllIds())
            ->paginate(20, 'item_' . $sortby[0], $sortby[1]);
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));

        $this->_application->setData(array(
            'items' => $page->getElements()->with('Feed'),
            'pages' => $pages,
            'page' => $page,
            'sortby' => $items_sortby,
            'sortby_allowed' => $items_sortby_allowed,
        ));
    }
}