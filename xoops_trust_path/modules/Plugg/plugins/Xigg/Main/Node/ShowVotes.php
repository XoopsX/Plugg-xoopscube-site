<?php
class Plugg_Xigg_Main_Node_ShowVotes extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $vote_view = $context->request->getAsStr('vote_view', 'newest');
        $perpage = $context->plugin->getParam('numberOfVotesOnPage');
        if ($vote_view == 'oldest') {
            $pages = $this->_application->node->paginateVotes($perpage, 'vote_created', 'ASC');
        } else {
            $vote_view = 'newest';
            $pages = $this->_application->node->paginateVotes($perpage, 'vote_created', 'DESC');
        }
        $page = $pages->getValidPage($context->request->getAsInt('vote_page', 1));
        $votes = $page->getElements();
        $votes = $votes->with('User');
        $this->_application->setData(array(
            'vote_pages' => $pages,
            'votes'      => $votes,
            'vote_view'  => $vote_view,
            'vote_page'  => $page->getPageNumber()
        ));
        $context->response->setPageInfo($context->plugin->_('Listing votes'));
    }
}