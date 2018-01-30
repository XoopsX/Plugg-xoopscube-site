<?php
class Plugg_Xigg_Main_RSS_ShowVotes extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$node = $this->getNodeById($context, 'node_id')) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/rss'
            ));
            return;
        }
        $vote_view = $context->request->getAsStr('vote_view', 'newest');
        $perpage = $context->plugin->getParam('numberOfVotesOnPage');
        $this->_application->setData(array(
            'node' => $node,
            'votes' => $node->paginateVotes($perpage, 'vote_created', 'DESC')
                ->getValidPage($context->request->getAsInt('vote_page', 1))
                ->getElements()
        ));
    }
}