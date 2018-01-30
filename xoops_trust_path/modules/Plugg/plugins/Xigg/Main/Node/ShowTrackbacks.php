<?php
class Plugg_Xigg_Main_Node_ShowTrackbacks extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $trackback_view = $context->request->getAsStr('trackback_view', 'newest');
        $perpage = $context->plugin->getParam('numberOfTrackbacksOnPage');
        if ($trackback_view == 'oldest') {
            $pages = $this->_application->node->paginateTrackbacks($perpage, 'trackback_created', 'ASC');
        } else {
            $trackback_view = 'newest';
            $pages = $this->_application->node->paginateTrackbacks($perpage, 'trackback_created', 'DESC');
        }
        $page = $pages->getValidPage($context->request->getAsInt('trackback_page', 1));
        $this->_application->setData(array(
            'trackback_pages' => $pages,
            'trackback_page'  => $page,
            'trackback_view'  => $trackback_view)
        );
        $context->response->setPageInfo($context->plugin->_('Listing trackbacks'));
    }
}