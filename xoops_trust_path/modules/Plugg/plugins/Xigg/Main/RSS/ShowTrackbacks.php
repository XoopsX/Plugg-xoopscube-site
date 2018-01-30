<?php
class Plugg_Xigg_Main_RSS_ShowTrackbacks extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$node = $this->getNodeById($context, 'node_id')) {
            $context->response->setError($context->plugin->_('Invalid request'), array('path' => '/rss'));
            return;
        }
        $trackback_view = $context->request->getAsStr('trackback_view', 'newest');
        $perpage = $context->plugin->getParam('numberOfTrackbacksOnPage');
        $this->_application->setData(array(
            'node' => $node,
            'trackbacks' => $node->paginateTrackbacks($perpage, 'trackback_created', 'DESC')
                ->getValidPage($context->request->getAsInt('trackback_page', 1))
                ->getElements()
        ));
    }
}