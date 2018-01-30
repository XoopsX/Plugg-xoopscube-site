<?php
class Plugg_Xigg_Admin_Node_Details extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $node = $this->_application->node;
        $comment_pages = $context->plugin->getModel()->Comment
            ->paginateByNodeAndCriteria($node->getId(), Sabai_Model_Criteria::createValue('comment_parent', 'NULL'), 10, 'comment_created', 'DESC');
        $comments = $comment_pages->getValidPage(1)->getElements()->with('User')->with('DescendantsCount');
        $trackback_pages = $node->paginateTrackbacks(10, 'trackback_created', 'DESC');
        $trackbacks = $trackback_pages->getValidPage(1)->getElements();
        $vote_pages = $node->paginateVotes(10, 'vote_created', 'DESC');
        $votes = $vote_pages->getValidPage(1)->getElements()->with('User');
        $this->_application->setData(array(
            'comment_pages'            => $comment_pages,
            'comment_objects'          => $comments,
            'comment_page_requested'   => 1,
            'comment_sortby'           => '',
            'trackback_pages'          => $trackback_pages,
            'trackback_objects'        => $trackbacks,
            'trackback_page_requested' => 1,
            'trackback_sortby'         => '',
            'vote_pages'               => $vote_pages,
            'vote_objects'             => $votes,
            'vote_page_requested'      => 1,
            'vote_sortby'              => ''
        ));
    }
}